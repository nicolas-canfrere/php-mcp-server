<?php

declare(strict_types=1);

namespace App\Mcp\JsonRpc;

final readonly class JsonRpcErrorResponse implements JsonRpcIdentifiableInterface, JsonRpcMessageInterface, \JsonSerializable
{
    public function __construct(
        private JsonRpcVersionEnum $jsonRpc,
        private string|int|null $id,
        private JsonRpcError $error,
    ) {
    }

    public function getError(): JsonRpcError
    {
        return $this->error;
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function getJsonRpc(): JsonRpcVersionEnum
    {
        return $this->jsonRpc;
    }

    /**
     * @return array{
     *     jsonrpc: string,
     *     id: int|string|null,
     *     error: JsonRpcError
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'jsonrpc' => $this->jsonRpc->value,
            'id' => $this->id,
            'error' => $this->error,
        ];
    }
}
