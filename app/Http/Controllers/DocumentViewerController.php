<?php

namespace App\Http\Controllers;

use App\Data\FilePreview;
use App\Services\FilePreviewService;
use App\Services\SvgPreviewSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;

class DocumentViewerController extends Controller
{
    public function show(Request $request, string $source, string $record, string $file, FilePreviewService $previews)
    {
        $preview = $previews->resolve($request->user(), $source, $record, $file);
        $metadata = array_merge($preview->metadata(), [
            'returnUrl' => $this->safeReturnUrl($request),
            'embedded' => $request->boolean('embed'),
        ]);

        return response()
            ->view('files.viewer', compact('preview', 'metadata'))
            ->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'same-origin')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }

    public function content(Request $request, string $source, string $record, string $file, FilePreviewService $previews, SvgPreviewSanitizer $svg)
    {
        $preview = $previews->resolve($request->user(), $source, $record, $file);
        abort_unless($preview->previewable, 415, $preview->unavailableReason ?? 'This file cannot be previewed.');

        $headers = $this->headers($preview, 'inline');
        if ($preview->extension === 'svg') {
            $contents = Storage::disk($preview->disk)->get($preview->path);

            return response($svg->sanitize($contents), 200, array_merge($headers, [
                'Content-Type' => 'image/svg+xml; charset=UTF-8',
                'Content-Security-Policy' => "sandbox; default-src 'none'; img-src data:",
            ]));
        }

        return response()->file(Storage::disk($preview->disk)->path($preview->path), $headers)
            ->setAutoEtag()
            ->setAutoLastModified();
    }

    public function download(Request $request, string $source, string $record, string $file, FilePreviewService $previews)
    {
        $preview = $previews->resolve($request->user(), $source, $record, $file);

        return response()->download(
            Storage::disk($preview->disk)->path($preview->path),
            $preview->name,
            $this->headers($preview, 'attachment')
        );
    }

    private function headers(FilePreview $preview, string $disposition): array
    {
        return [
            'Content-Type' => $preview->mime,
            'Content-Disposition' => HeaderUtils::makeDisposition($disposition, $preview->name, 'file.'.$preview->extension),
            'Cache-Control' => 'private, no-store',
            'Accept-Ranges' => 'bytes',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "sandbox; default-src 'none'; img-src 'self' data: blob:; media-src 'self' blob:; style-src 'unsafe-inline'",
        ];
    }

    private function safeReturnUrl(Request $request): string
    {
        $candidate = $request->query('return');
        if (!is_string($candidate) || !str_starts_with($candidate, '/') || str_starts_with($candidate, '//') || str_contains($candidate, "\0")) {
            return route('dashboard');
        }

        return $candidate;
    }
}
