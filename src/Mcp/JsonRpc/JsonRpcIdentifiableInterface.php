<?php

declare(strict_types=1);

namespace App\Mcp\JsonRpc;

interface JsonRpcIdentifiableInterface
{
    public function getId(): string|int|null;
}
