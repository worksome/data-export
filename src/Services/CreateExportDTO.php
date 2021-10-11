<?php

namespace Worksome\DataExport\Services;

use JetBrains\PhpStorm\Pure;

class CreateExportDTO
{
    #[Pure]
    public function __construct(
        private int $userId,
        private int $accountId,
        private string $accountType,
        private string $type,
        private string $generatorType,
        private array $deliveries,
        private array $args,
        private int|null $impersonatorId = null,
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

    public function getImpersonatorId(): ?int
    {
        return $this->impersonatorId;
    }
}
