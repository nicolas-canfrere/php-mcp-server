<?php

declare(strict_types=1);

namespace App\Mcp\JsonRpc;

use App\Mcp\McpMethodEnum;

/**
 * @phpstan-type RequestPayload array{
 *     jsonrpc?: mixed,
 *     method?: mixed,
 *     id?: mixed,
 *     params?: array<string, mixed>
 * }
 */
final class JsonRpcRequestFactory implements JsonRpcRequestFactoryInterface
{
    public function createFromString(string $requestPayload): JsonRpcRequest|JsonRpcErrorResponse
    {
        try {
            /** @var RequestPayload $payload */
            $payload = json_decode($requestPayload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return $this->createError(JsonRpcErrorCodeEnum::PARSE_ERROR, $exception->getMessage());
        }
        if (empty($payload['jsonrpc'])
            || !is_string($payload['jsonrpc'])
            || null === JsonRpcVersionEnum::tryFrom($payload['jsonrpc'])
        ) {
            return $this->createError(JsonRpcErrorCodeEnum::INVALID_REQUEST, 'Invalid JsonRpc version.');
        }
        if (empty($payload['method']) || !is_string($payload['method'])) {
            return $this->createError(JsonRpcErrorCodeEnum::INVALID_REQUEST, 'Method name not set.');
        }
        if (null === $method = McpMethodEnum::tryFrom($payload['method'])) {
            return $this->createError(JsonRpcErrorCodeEnum::METHOD_NOT_FOUND, 'Method not found.');
        }
        if (array_key_exists('id', $payload)) {
            if (!is_int($payload['id']) && !is_string($payload['id']) && !is_null($payload['id'])) {
                return $this->createError(JsonRpcErrorCodeEnum::INVALID_REQUEST, 'Id must be integer or string or null.');
            }
        }

        return new JsonRpcRequest(
            JsonRpcVersionEnum::from($payload['jsonrpc']),
            $payload['id'] ?? null,
            $method,
            $payload['params'] ?? [],
        );
    }

    private function createError(JsonRpcErrorCodeEnum $code, string $message): JsonRpcErrorResponse
    {
        return new JsonRpcErrorResponse(
            JsonRpcVersionEnum::VERSION_2,
            null,
            new JsonRpcError($code, $message),
        );
    }
}
