<?php

declare(strict_types=1);

namespace App\Mcp\Registry;

final class ResourcesRegistry extends AbstractCapabilityRegistry
{
    public function getName(): string
    {
        return 'resources';
    }

    public function getParameters(): array
    {
        return ['listChanged' => false];
    }
}
