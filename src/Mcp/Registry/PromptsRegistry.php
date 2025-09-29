<?php

declare(strict_types=1);

namespace App\Mcp\Registry;

final class PromptsRegistry extends AbstractCapabilityRegistry
{
    public function getName(): string
    {
        return 'prompts';
    }

    public function getParameters(): array
    {
        return ['listChanged' => false];
    }
}
