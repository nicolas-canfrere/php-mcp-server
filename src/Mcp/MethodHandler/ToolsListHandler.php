<?php

declare(strict_types=1);

namespace App\Mcp\MethodHandler;

use App\Mcp\Exception\NoToolsAvailableException;
use App\Mcp\JsonRpc\JsonRpcRequest;
use App\Mcp\JsonRpc\JsonRpcResultResponse;
use App\Mcp\McpMethodEnum;
use App\Mcp\McpMethodHandlerInterface;
use App\Mcp\Registry\CapabilityRegistryInterface;

final readonly class ToolsListHandler implements McpMethodHandlerInterface
{
    public function __construct(
        private CapabilityRegistryInterface $toolsRegistry,
    ) {
    }

    public function supports(JsonRpcRequest $jsonRpcRequest): bool
    {
        return McpMethodEnum::TOOLS_LIST === $jsonRpcRequest->getMethod();
    }

    public function handle(JsonRpcRequest $jsonRpcRequest): JsonRpcResultResponse
    {
        if (!$this->toolsRegistry->hasCapabilities()) {
            throw new NoToolsAvailableException();
        }
        $result = [
            $this->toolsRegistry->getName() => $this->formatCapabilitiesForResponse(),
        ];

        return new JsonRpcResultResponse(
            $jsonRpcRequest->getJsonRpc(),
            $jsonRpcRequest->getId(),
            $result,
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function formatCapabilitiesForResponse(): array
    {
        $definitions = [];
        foreach ($this->toolsRegistry->getAllCapabilities() as $capability) {
            $definitions[] = $capability->getDefinition();
        }

        return $definitions;
    }
}
