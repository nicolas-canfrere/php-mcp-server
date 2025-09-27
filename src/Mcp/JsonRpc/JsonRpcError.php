<?php

declare(strict_types=1);

namespace App\Mcp\JsonRpc;

final readonly class JsonRpcError
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private JsonRpcErrorCodeEnum $code,
        private string $message,
        private ?array $data = null,
    ) {
    }

    public function getCode(): JsonRpcErrorCodeEnum
    {
        return $this->code;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
