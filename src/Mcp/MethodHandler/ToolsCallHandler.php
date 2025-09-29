<?php

declare(strict_types=1);

namespace App\Mcp\MethodHandler;

use App\Mcp\Exception\ToolNotFoundException;
use App\Mcp\JsonRpc\JsonRpcError;
use App\Mcp\JsonRpc\JsonRpcErrorCodeEnum;
use App\Mcp\JsonRpc\JsonRpcErrorResponse;
use App\Mcp\JsonRpc\JsonRpcRequest;
use App\Mcp\JsonRpc\JsonRpcResultResponse;
use App\Mcp\McpMethodEnum;
use App\Mcp\McpMethodHandlerInterface;
use App\Mcp\Registry\CapabilityRegistryInterface;

final readonly class ToolsCallHandler implements McpMethodHandlerInterface
{
    public function __construct(
        private CapabilityRegistryInterface $toolsRegistry,
    ) {
    }

    public function supports(JsonRpcRequest $jsonRpcRequest): bool
    {
        return McpMethodEnum::TOOLS_CALL === $jsonRpcRequest->getMethod();
    }

    public function handle(JsonRpcRequest $jsonRpcRequest): JsonRpcResultResponse|JsonRpcErrorResponse
    {
        try {
            /** @var string $toolName */
            $toolName = $jsonRpcRequest->getParams()['name'];
            /** @var array<string, mixed> $toolArguments */
            $toolArguments = $jsonRpcRequest->getParams()['arguments'];
            $tool = $this->toolsRegistry->getCapability($toolName);
            if (null === $tool) {
                throw new ToolNotFoundException(
                    'No such tool "' . $toolName . '" exists',
                );
            }

            return new JsonRpcResultResponse(
                $jsonRpcRequest->getJsonRpc(),
                $jsonRpcRequest->getId(),
                $tool->handle($toolArguments),
            );
        } catch (\Throwable $exception) {
            return new JsonRpcErrorResponse(
                $jsonRpcRequest->getJsonRpc(),
                $jsonRpcRequest->getId(),
                new JsonRpcError(
                    JsonRpcErrorCodeEnum::INTERNAL_ERROR,
                    $exception->getMessage(),
                )
            );
        }
    }
}
