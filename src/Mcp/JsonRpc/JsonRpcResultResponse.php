<?php

declare(strict_types=1);

namespace App\Mcp\JsonRpc;

final readonly class JsonRpcResultResponse implements JsonRpcIdentifiableInterface, JsonRpcMessageInterface, \JsonSerializable
{
    /**
     * @param array<string, mixed> $result
     */
    public function __construct(
        private JsonRpcVersionEnum $jsonRpc,
        private string|int|null $id,
        private array $result,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getResult(): array
    {
        return $this->result;
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
     *     result: array<string, mixed>
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'jsonrpc' => $this->jsonRpc->value,
            'id' => $this->id,
            'result' => $this->result,
        ];
    }
}
