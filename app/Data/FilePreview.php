<?php

namespace App\Data;

final readonly class FilePreview
{
    public function __construct(
        public string $source,
        public string $record,
        public string $file,
        public string $disk,
        public string $path,
        public string $name,
        public string $extension,
        public string $mime,
        public int $size,
        public string $kind,
        public bool $previewable,
        public ?string $unavailableReason = null,
    ) {
    }

    public function routeParameters(): array
    {
        return [$this->source, $this->record, $this->file];
    }

    public function metadata(): array
    {
        return [
            'name' => $this->name,
            'extension' => strtoupper($this->extension ?: 'FILE'),
            'mime' => $this->mime,
            'size' => $this->size,
            'kind' => $this->kind,
            'previewable' => $this->previewable,
            'unavailableReason' => $this->unavailableReason,
            'contentUrl' => $this->previewable ? route('files.content', $this->routeParameters()) : null,
            'downloadUrl' => route('files.download', $this->routeParameters()),
        ];
    }
}
