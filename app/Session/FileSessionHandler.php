<?php

namespace App\Session;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use RuntimeException;
use SessionHandler;
use Symfony\Component\Finder\Finder;

class FileSessionHandler extends SessionHandler
{
    protected Filesystem $files;

    protected string $path;

    protected int $minutes;

    public function __construct(Filesystem $files, string $path, int $minutes)
    {
        $this->files = $files;
        $this->path = $path;
        $this->minutes = $minutes;
    }

    public function create_sid(): string
    {
        return session_create_id() ?: throw new RuntimeException('Unable to create a session ID.');
    }

    public function validateId($id): bool
    {
        return $this->files->isFile($this->path.'/'.$id);
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        if ($this->files->isFile($path = $this->path.'/'.$id) &&
            $this->files->lastModified($path) >= Carbon::now()->subMinutes($this->minutes)->getTimestamp()) {
            return $this->files->sharedGet($path);
        }

        return '';
    }

    public function write(string $id, string $data): bool
    {
        $this->files->put($this->path.'/'.$id, $data, true);

        return true;
    }

    public function destroy(string $id): bool
    {
        $this->files->delete($this->path.'/'.$id);

        return true;
    }

    public function gc(int $maxLifetime): int|false
    {
        $files = Finder::create()
            ->in($this->path)
            ->files()
            ->ignoreDotFiles(true)
            ->date('<= now - '.$maxLifetime.' seconds');

        $deletedSessions = 0;

        foreach ($files as $file) {
            $this->files->delete($file->getRealPath());
            $deletedSessions++;
        }

        return $deletedSessions;
    }
}
