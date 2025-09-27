<?php

declare(strict_types=1);

namespace App\Mcp\JsonRpc;

final readonly class JsonRpcRequest implements JsonRpcIdentifiableInterface, JsonRpcMessageInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        private JsonRpcVersionEnum $jsonRpc,
        private string|int|null $id,
        private string $method,
        private array $params,
    ) {
    }

    public function isNotification(): bool
    {
        return null === $this->id;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function getJsonRpc(): JsonRpcVersionEnum
    {
        return $this->jsonRpc;
    }
}
