<?php

declare(strict_types=1);

namespace App\Mcp\JsonRpc;

interface JsonRpcRequestFactoryInterface
{
    public function createFromString(string $requestPayload): JsonRpcRequest|JsonRpcErrorResponse;
}
