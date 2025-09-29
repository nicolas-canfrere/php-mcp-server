<?php

declare(strict_types=1);

namespace App\Mcp\Registry;

final class ToolsRegistry extends AbstractCapabilityRegistry
{
    public function getName(): string
    {
        return 'tools';
    }

    public function getParameters(): array
    {
        return ['listChanged' => false];
    }
}
