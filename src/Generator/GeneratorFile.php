<?php

namespace Worksome\DataExport\Generator;

class GeneratorFile
{
    public function __construct(
        private readonly string $path,
        private readonly int $size,
        private readonly string $url,
        private readonly int $count,
        private readonly string $mimeType,
    ) {
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }
}
