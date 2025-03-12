<?php

namespace Worksome\DataExport\Services;

use Carbon\Carbon;
use Worksome\DataExport\Enums\ExportStatus;
use Worksome\DataExport\Models\Export;

class CreateExport
{
    private CreateExportDTO $dto;

    public function fromDTO(CreateExportDTO $dto): self
    {
        $this->dto = $dto;

        return $this;
    }

    public function run(): Export
    {
        $dtoArgs = $this->dto->getArgs();
        $dateTo = Carbon::parse($dtoArgs['dateTo'])->toDateString();
        $dateFrom = Carbon::parse($dtoArgs['dateFrom'])->toDateString();

        return Export::create([
            'user_id' => $this->dto->getUserId(),
            'impersonator_id' => $this->dto->getImpersonatorId(),
            'account_id' => $this->dto->getAccountId(),
            'account_type' => $this->dto->getAccountType(),
            'status' => ExportStatus::Awaiting,
            'type' => $this->dto->getType(),
            'generator_type' => $this->dto->getGeneratorType(),
            'deliveries' => $this->dto->getDeliveries(),
            'args' => array_merge(
                $dtoArgs,
                [
                    'dateTo' => $dateTo,
                    'dateFrom' => $dateFrom,
                ]
            ),
        ]);
    }
}
