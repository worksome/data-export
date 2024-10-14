<?php

namespace Worksome\DataExport\Generator;

use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Worksome\DataExport\Generator\Contracts\GeneratorDriver;
use Worksome\DataExport\Processor\ProcessorData;

class CsvDriver implements GeneratorDriver
{
    public function generate(ProcessorData $processorData): GeneratorFile
    {
        $csv = $this->exportToCsv($processorData);

        $filename = sprintf(
            'export-%s-%s-%s',
            $processorData->getType(),
            Carbon::now()->format('Y-m-d'),
            Str::random(40)
        );

        return $this->saveToStorage($filename, $csv, $processorData);
    }

    public function exportToCsv(ProcessorData $processorData)
    {
        $spreadsheet = $this->toSpreadsheet($processorData->getData());
        $writer = $this->toCsvWriter($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function saveToStorage($filenameWithoutExtension, $content, ProcessorData $processorData): GeneratorFile
    {
        $filepath = sprintf('exports/%s.csv', $filenameWithoutExtension);

        Storage::put($filepath, $content);

        return new GeneratorFile(
            path: $filepath,
            size: Storage::size($filepath),
            url: Storage::url($filepath),
            count: count($processorData->getData()),
            mimeType: 'text/csv',
        );
    }

    public function toCsvWriter(Spreadsheet $spreadsheet): Csv
    {
        $writer = new Csv($spreadsheet);
        $writer->setEnclosureRequired(false);
        $writer->setLineEnding("\r\n");
        $writer->setDelimiter(',');

        return $writer;
    }

    public function toSpreadsheet(array $entries): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $data = Collection::make($entries);
        $data = $data->map(fn ($item) => $item);

        $spreadsheetData = $data
            ->prepend(array_keys($data->first() ?? []))
            ->toArray();

        $sheet->fromArray($spreadsheetData);

        return $spreadsheet;
    }

    public function toStream(IWriter $writer): Stream
    {
        // Write the spreadsheet to a resource.
        $resource = fopen('php://temp', 'w+');

        $writer->save($resource);

        // Rewind the resource and convert to PSR stream
        rewind($resource);

        return new Stream($resource);
    }
}
