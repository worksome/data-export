<?php

namespace Worksome\DataExport\Services;

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
        return Export::create([
            'user_id' => $this->dto->getUserId(),
            'account_id' => $this->dto->getAccountId(),
            'account_type' => $this->dto->getAccountType(),
            'status' => ExportStatus::AWAITING,
            'type' => $this->dto->getType(),
            'generator_type' => $this->dto->getGeneratorType(),
            'deliveries' => $this->dto->getDeliveries(),
            'args' => $this->dto->getArgs(),
        ]);
    }
}
