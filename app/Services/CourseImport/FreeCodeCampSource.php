<?php

namespace App\Services\CourseImport;

use App\Contracts\CourseContentSource;
use App\Exceptions\CourseContentException;
use Illuminate\Support\Str;

class FreeCodeCampSource implements CourseContentSource
{
    private const API = 'https://api.github.com';

    private const RAW = 'https://raw.githubusercontent.com';

    private const REPOSITORY = 'freeCodeCamp/freeCodeCamp';

    private const ROOT = 'curriculum/challenges/english/blocks';

    private const LICENSE_SHA256 = '3006a6f8636a327498f20182fa54a7b39a92f6c8bf814002c7ebf902611b50c0';

    private const MAX_FILE_BYTES = 40000;

    private const MAX_TOTAL_BYTES = 300000;

    public function __construct(private readonly GitHubContentClient $client) {}

    public static function licenseVerified(string $license): bool
    {
        return hash('sha256', trim(preg_replace('/\s+/', ' ', $license))) === self::LICENSE_SHA256;
    }

    public static function includesLicense(string $html, string $license): bool
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return str_contains(trim(preg_replace('/\s+/', ' ', $text)), trim(preg_replace('/\s+/', ' ', $license)));
    }

    public function key(): string
    {
        return 'freecodecamp';
    }

    public function name(): string
    {
        return 'freeCodeCamp curriculum challenges (Markdown lessons only)';
    }

    public function inspect(string $path, int $limit = 12, ?string $revision = null): array
    {
        $path = $this->validatePath($path);
        $limit = min(max($limit, 1), 12);
        if ($revision === null) {
            $commit = $this->getJson(self::API.'/repos/'.self::REPOSITORY.'/commits/main');
            $revision = (string) ($commit['sha'] ?? '');
        }

        if (! preg_match('/^[a-f0-9]{40}$/', $revision)) {
            throw new CourseContentException('The source repository did not provide a valid revision.');
        }

        $license = $this->getText(self::RAW.'/'.self::REPOSITORY.'/'.$revision.'/LICENSE.md', 8000);
        if (! self::licenseVerified($license)) {
            throw new CourseContentException('The source licence could not be verified; no course content was imported.');
        }

        // Git trees avoid the Contents API's 1,000-entry truncation in curriculum/blocks.
        $treeId = $revision;
        foreach (explode('/', $path) as $segment) {
            $tree = $this->tree($treeId);
            $this->assertNoAdditionalNotices($tree, $treeId === $revision);
            $directory = collect($tree)->first(fn ($entry) => ($entry['type'] ?? null) === 'tree' && ($entry['path'] ?? null) === $segment);
            $treeId = (string) ($directory['sha'] ?? '');
            if (! preg_match('/^[a-f0-9]{40}$/', $treeId)) {
                throw new CourseContentException('The selected curriculum path is not a supported directory.');
            }
        }
        $tree = $this->tree($treeId);
        $this->assertNoAdditionalNotices($tree);
        $entries = array_map(fn ($entry) => [
            'name' => $entry['path'] ?? '',
            'type' => ($entry['type'] ?? null) === 'blob' && ($entry['mode'] ?? null) === '100644' ? 'file' : 'unsupported',
            'size' => $entry['size'] ?? 0,
        ], $tree);

        $candidateCount = collect($entries)->filter(fn ($entry) => is_array($entry)
            && ($entry['type'] ?? null) === 'file'
            && preg_match('/^[a-f0-9]{24}\.md$/', (string) ($entry['name'] ?? '')))->count();
        $selected = collect($entries)
            ->filter(fn ($entry) => is_array($entry)
                && ($entry['type'] ?? null) === 'file'
                && preg_match('/^[a-f0-9]{24}\.md$/', (string) ($entry['name'] ?? '')))
            ->sortBy('name')
            ->take($limit)
            ->values();

        $files = $selected->filter(fn ($entry) => (int) ($entry['size'] ?? 0) > 0 && (int) $entry['size'] <= self::MAX_FILE_BYTES)->values();

        if ($files->isEmpty()) {
            throw new CourseContentException('No supported Markdown challenge files were found in that curriculum path.');
        }

        $lessons = [];
        $bytes = 0;

        foreach ($files as $entry) {
            $url = self::RAW.'/'.self::REPOSITORY.'/'.$revision.'/'.$path.'/'.$entry['name'];
            $markdown = $this->getText($url, self::MAX_FILE_BYTES);
            $bytes += strlen($markdown);

            if ($bytes > self::MAX_TOTAL_BYTES) {
                throw new CourseContentException('The selected curriculum exceeds the safe import size limit.');
            }

            $lesson = $this->parseChallenge($markdown, $entry['name']);
            if ($lesson === null) {
                continue;
            }

            $lesson['source_url'] = 'https://github.com/'.self::REPOSITORY.'/blob/'.$revision.'/'.$path.'/'.$entry['name'];
            $lessons[] = $lesson;
        }

        if ($lessons === []) {
            throw new CourseContentException('The selected Markdown files did not contain supported lesson descriptions.');
        }

        $attribution = 'Copyright (c) 2014, freeCodeCamp. Licensed under the BSD 3-Clause License. Source: https://github.com/'.self::REPOSITORY."/tree/{$revision}/{$path}. The complete copyright notice, licence conditions, and disclaimer must be retained with any redistribution.";

        return [
            'provider' => 'GitHub',
            'repository' => self::REPOSITORY,
            'path' => $path,
            'revision' => $revision,
            'source_url' => 'https://github.com/'.self::REPOSITORY.'/tree/'.$revision.'/'.$path,
            'original_title' => Str::headline(basename($path)),
            'original_author' => 'freeCodeCamp',
            'license_id' => 'BSD-3-Clause',
            'license_version' => null,
            'license_url' => 'https://github.com/'.self::REPOSITORY.'/blob/'.$revision.'/LICENSE.md',
            'license_text' => $license,
            'attribution' => $attribution,
            'lessons' => $lessons,
            'skipped_files' => max(0, $selected->count() - count($lessons)),
            'files_available' => $candidateCount,
            'files_selected' => $selected->count(),
            'content_bytes' => $bytes,
            'is_outline' => false,
        ];
    }

    public function validatePath(string $path): string
    {
        if (! preg_match('/^'.preg_quote(self::ROOT, '/').'\/[a-z0-9]+(?:-[a-z0-9]+)*$/', $path)) {
            throw new CourseContentException('Choose a single freeCodeCamp curriculum block under '.self::ROOT.'.');
        }

        if (basename($path) !== 'algorithms') {
            throw new CourseContentException('Only the reviewed algorithms block is currently supported. Other curriculum blocks need a licence and format review; use their external freeCodeCamp link meanwhile.');
        }

        return $path;
    }

    private function tree(string $treeId): array
    {
        $document = $this->getJson(self::API.'/repos/'.self::REPOSITORY.'/git/trees/'.$treeId);
        if (! isset($document['tree']) || ! is_array($document['tree']) || ! array_is_list($document['tree']) || ($document['truncated'] ?? true)) {
            throw new CourseContentException('The source directory listing is incomplete or invalid.');
        }

        return $document['tree'];
    }

    private function getJson(string $url): array
    {
        $data = json_decode($this->client->get($url, 'application/vnd.github+json', 1500000), true);
        if (! is_array($data)) {
            throw new CourseContentException('The source repository returned an invalid response.');
        }

        return $data;
    }

    private function getText(string $url, int $maxBytes): string
    {
        return $this->client->get($url, 'text/plain', $maxBytes);
    }

    private function assertNoAdditionalNotices(array $entries, bool $root = false): void
    {
        foreach ($entries as $entry) {
            $name = (string) ($entry['path'] ?? '');
            if ($root && $name === 'LICENSE.md') {
                continue;
            }
            if (($entry['type'] ?? null) === 'blob'
                && preg_match('/^(license|licence|copying|notice)(?:\.|$)/i', $name)) {
                throw new CourseContentException('The selected curriculum has an additional licence or notice that this importer has not verified.');
            }
        }
    }

    private function parseChallenge(string $markdown, string $filename): ?array
    {
        if (! preg_match('/\A---\s*\R(.*?)\R---\s*\R/s', $markdown, $frontMatter)) {
            return null;
        }
        if (preg_match('/^license:\s*(.+)$/mi', $frontMatter[1], $licenseMatch)
            && ! in_array(strtolower(trim($licenseMatch[1], " \"'")), ['bsd-3-clause', 'bsd 3-clause'], true)) {
            throw new CourseContentException('A curriculum file declares a licence that this importer has not verified.');
        }

        $title = null;
        if (preg_match('/^title:\s*["\']?(.+?)["\']?\s*$/m', $frontMatter[1], $match)) {
            $title = trim($match[1]);
        }

        if (! preg_match('/^# --description--\s*\R(.*?)(?=^# --[a-z-]+--|\z)/ms', $markdown, $description)) {
            return null;
        }

        // The reviewed block has no third-party per-file notices; fail closed on new ones.
        if (preg_match('/^\s*(?:licence|copyright|attribution|source)\s*:/mi', $frontMatter[1])) {
            throw new CourseContentException('A curriculum file has an additional source or rights notice that requires review.');
        }
        $content = trim($description[1]);
        if ($content === '') {
            return null;
        }

        return [
            'original_id' => pathinfo($filename, PATHINFO_FILENAME),
            'title' => Str::limit(strip_tags($title ?: pathinfo($filename, PATHINFO_FILENAME)), 255, ''),
            'content' => $content,
            'duration_minutes' => max(1, (int) ceil(str_word_count(strip_tags($content)) / 200)),
            'learning_outcomes' => [],
        ];
    }
}
