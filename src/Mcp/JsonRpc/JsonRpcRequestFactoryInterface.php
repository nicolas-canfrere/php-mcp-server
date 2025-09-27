<?php

declare(strict_types=1);

namespace App\Mcp\JsonRpc;

interface JsonRpcRequestFactoryInterface
{
    public function createFromArray(string $payloadAsJsonString): JsonRpcRequest|JsonRpcErrorResponse;
}
