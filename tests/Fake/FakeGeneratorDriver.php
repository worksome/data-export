<?php

namespace Worksome\DataExport\Tests\Fake;

use Worksome\DataExport\Generator\Contracts\GeneratorDriver;
use Worksome\DataExport\Generator\GeneratorFile;
use Worksome\DataExport\Processor\ProcessorData;

class FakeGeneratorDriver implements GeneratorDriver
{
    public function generate(ProcessorData $processorData): GeneratorFile
    {
        return new GeneratorFile(
            path: '/',
            size: 1,
            url: 'https://worksome.eu',
            count: 1,
            mimeType: 'application/text',
        );
    }
}
