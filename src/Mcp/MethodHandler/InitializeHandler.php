<?php

declare(strict_types=1);

namespace App\Mcp\MethodHandler;

use App\Mcp\JsonRpc\JsonRpcRequest;
use App\Mcp\JsonRpc\JsonRpcResultResponse;
use App\Mcp\McpMethodEnum;
use App\Mcp\McpMethodHandlerInterface;
use App\Mcp\Registry\CapabilityRegistryInterface;

final readonly class InitializeHandler implements McpMethodHandlerInterface
{
    /**
     * @param iterable<CapabilityRegistryInterface> $registries
     */
    public function __construct(
        private string   $name,
        private string   $title,
        private string   $version,
        private string   $protocolVersion,
        private iterable $registries,
    ) {
    }

    public function supports(JsonRpcRequest $jsonRpcRequest): bool
    {
        return McpMethodEnum::INITIALIZE === $jsonRpcRequest->getMethod();
    }

    public function handle(JsonRpcRequest $jsonRpcRequest): JsonRpcResultResponse
    {
        $capabilities = [];
        foreach ($this->registries as $registry) {
            if ($registry->hasCapabilities()) {
                $capabilities[$registry->getName()] = $registry->getParameters();
            }
        }

        return new JsonRpcResultResponse(
            $jsonRpcRequest->getJsonRpc(),
            $jsonRpcRequest->getId(),
            [
                'protocolVersion' => $this->protocolVersion,
                'capabilities' => $capabilities,
                'serverInfo' => [
                    'name' => $this->name,
                    'version' => $this->version,
                    'title' => $this->title,
                ],
                'instructions' => '',
            ]
        );
    }
}
