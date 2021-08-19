<?php

namespace Worksome\DataExport\Processor;

use Carbon\Carbon;
use Worksome\DataExport\Models\Export;
use Worksome\DataExport\Processor\Contracts\Processor as ProcessorContract;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

abstract class SqlProcessor implements ProcessorContract
{
    protected $sql = null;

    /**
     * Coulmns you would like to be included in the export
     * You can also rename the columns if wanted e.g.
     * ['email_address' =>> 'email']
     * @var array
     */
    protected $columns = [];

    public function __construct(
        private ConnectionInterface $connection,
    ) {}

    public function process(Export $export): ProcessorData
    {
        $data = $this->query($this->sql, $export->args);
        $data = $this->filter($data);

        return new ProcessorData($data);
    }

    protected function query($sqlFile, $sqlFileArgs = null): array
    {
        $from = isset($sqlFileArgs['dateFrom']) ? $sqlFileArgs['dateFrom'] : '1970-01-01';
        $to = isset($sqlFileArgs['dateTo']) ? $sqlFileArgs['dateTo'] : Carbon::now()->format('Y-m-d');

        $sql = sprintf(
            file_get_contents(App::basePath(sprintf('app/%s', $sqlFile))),
            implode(', ', $sqlFileArgs['companies']),
            $from,
            $to,
        );

        $results = $this->connection->select($this->connection->raw($sql));

        return $results;
    }

    protected function filter($data): array
    {
        $columns = $this->columns();
        $allowedColumns = $columns->keys()->all();

        // get only required columns
        $filtered = collect($data)->map(fn($item) => collect($item)->only($allowedColumns)->all());

        // update key (aka label for the file)
        $filtered = $filtered->map(function ($item) use ($columns) {
            return collect($item)->keyBy(function ($value, $key) use ($columns) {
                return $columns->get($key);
            })->all();
        });

        return $filtered->all();
    }

    protected function columns(): Collection
    {
        $columns = collect($this->columns)->keyBy(function ($value, $key) {
            if (is_numeric($key)) {
                return $value;
            } else {
                return $key;
            }
       });

        return $columns;
    }
}
