<?php

declare(strict_types=1);

namespace App\Mcp\JsonRpc;

interface JsonRpcMessageInterface
{
    public function getJsonRpc(): JsonRpcVersionEnum;
}
