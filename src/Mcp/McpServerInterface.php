<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Mcp\JsonRpc\JsonRpcMessageInterface;

interface McpServerInterface
{
    public function handle(string $requestPayload): JsonRpcMessageInterface;
}
