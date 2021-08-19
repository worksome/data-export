<?php

namespace Worksome\DataExport\Export;

use JetBrains\PhpStorm\Pure;

class CreateExportDTO
{
    #[Pure]
    public function __construct(
        private ?int $userId = null,
        private ?int $accountId = null,
        private ?string $accountType = null,
        private ?array $delivery = null,
        private ?string $type = null,
        private ?array $args = null
    )
    { }

    public static function fromArgs(array $args): self
    {
        return new self(
            userId: $args['userId'],
            accountId: $args['accountId'],
            accountType: $args['accountType'],
            delivery: $args['delivery'],
            type: $args['type'],
            args: $args['args'],
        );
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getDelivery()
    {
        return $this->delivery;
    }

    public function getArgs(): ?array
    {
        return $this->args;
    }
}
