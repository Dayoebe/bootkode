<?php

namespace App\Services\CourseImport;

use App\Contracts\CourseContentSource;
use InvalidArgumentException;

class CourseContentSourceRegistry
{
    /** @var array<string, CourseContentSource> */
    private array $sources;

    public function __construct(FreeCodeCampSource $freeCodeCamp)
    {
        $this->sources = [$freeCodeCamp->key() => $freeCodeCamp];
    }

    public function all(): array
    {
        return $this->sources;
    }

    public function get(string $key): CourseContentSource
    {
        return $this->sources[$key] ?? throw new InvalidArgumentException('Unknown course content source.');
    }
}
