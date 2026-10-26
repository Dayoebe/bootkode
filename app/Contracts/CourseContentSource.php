<?php

namespace App\Contracts;

interface CourseContentSource
{
    public function key(): string;

    public function name(): string;

    public function validatePath(string $path): string;

    public function inspect(string $path, int $limit = 12, ?string $revision = null): array;
}
