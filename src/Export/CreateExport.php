<?php

namespace Worksome\DataExport\Export;

use Worksome\DataExport\Enums\ExportStatus;
use Worksome\DataExport\Models\Export;

class CreateExport
{
    protected CreateExportDTO $dto;

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
            'delivery' => $this->dto->getDelivery(),
            'status' => ExportStatus::AWAITING,
            'type' => $this->dto->getType(),
            'args' => $this->dto->getArgs(),
        ]);
    }
}
