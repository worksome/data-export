<?php

namespace Worksome\DataExport\Services;

class CreateExportDTO
{
    public function __construct(
        private readonly int $userId,
        private readonly int $accountId,
        private readonly string $accountType,
        private readonly string $type,
        private readonly string $generatorType,
        private readonly array $deliveries,
        private readonly array $args,
        private readonly int|null $impersonatorId = null,
    ) {
    }

    public static function fromArgs(array $args): self
    {
        return new self(...$args);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getAccountType(): string
    {
        return $this->accountType;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getGeneratorType(): string
    {
        return $this->generatorType;
    }

    public function getDeliveries(): array
    {
        return $this->deliveries;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getImpersonatorId(): int|null
    {
        return $this->impersonatorId;
    }
}
