<?php

namespace Worksome\DataExport\Tests\Feature\Generator;

use Worksome\DataExport\Generator\GeneratorFile;

it('can initialize the generator file class', function () {
    $generatorFile = new GeneratorFile(
        path: '/',
        size: 1,
        url: 'https://worksome.eu',
        count: 1,
        mimeType: 'application/text',
    );

    expect($generatorFile->getPath())->toBe('/');
    expect($generatorFile->getSize())->toBe(1);
    expect($generatorFile->getUrl())->toBe('https://worksome.eu');
    expect($generatorFile->getCount())->toBe(1);
    expect($generatorFile->getMimeType())->toBe('application/text');
});
