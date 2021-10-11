<?php

namespace Worksome\DataExport\Generator;

class GeneratorFile
{
    public function __construct(
        private string $path,
        private int $size,
        private string $url,
        private int $count,
        private string $mimeType,
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
