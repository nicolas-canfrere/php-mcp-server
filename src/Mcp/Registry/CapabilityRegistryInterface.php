<?php

declare(strict_types=1);

namespace App\Mcp\Registry;

use App\Mcp\CapabilityInterface;

interface CapabilityRegistryInterface
{
    public function getName(): string;

    public function hasCapabilities(): bool;

    public function getCapability(string $name): ?CapabilityInterface;

    /**
     * @return CapabilityInterface[]
     */
    public function getAllCapabilities(): iterable;

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array;
}
