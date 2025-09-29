<?php

declare(strict_types=1);

namespace App\Mcp\Registry;

use App\Mcp\CapabilityInterface;

abstract class AbstractCapabilityRegistry implements CapabilityRegistryInterface
{
    /**
     * @param iterable<CapabilityInterface> $capabilities
     */
    public function __construct(
        protected iterable $capabilities,
    ) {
    }

    abstract public function getName(): string;

    public function hasCapabilities(): bool
    {
        return count($this->capabilities) > 0;
    }

    public function getCapability(string $name): ?CapabilityInterface
    {
        foreach ($this->capabilities as $capability) {
            if ($capability->getName() === $name) {
                return $capability;
            }
        }

        return null;
    }

    public function getAllCapabilities(): iterable
    {
        return $this->capabilities;
    }

    abstract public function getParameters(): array;
}
