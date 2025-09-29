<?php

declare(strict_types=1);

namespace App\Mcp;

interface CapabilityInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getDefinition(): array;

    public function getName(): string;

    public function handle(array $arguments): array;
}
