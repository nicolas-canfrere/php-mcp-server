<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Mcp\JsonRpc\JsonRpcErrorResponse;
use App\Mcp\JsonRpc\JsonRpcRequest;
use App\Mcp\JsonRpc\JsonRpcResultResponse;

interface McpMethodHandlerInterface
{
    public function supports(JsonRpcRequest $jsonRpcRequest): bool;

    public function handle(JsonRpcRequest $jsonRpcRequest): JsonRpcResultResponse|JsonRpcErrorResponse;
}
