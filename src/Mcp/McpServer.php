<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Mcp\JsonRpc\JsonRpcError;
use App\Mcp\JsonRpc\JsonRpcErrorCodeEnum;
use App\Mcp\JsonRpc\JsonRpcErrorResponse;
use App\Mcp\JsonRpc\JsonRpcMessageInterface;
use App\Mcp\JsonRpc\JsonRpcRequestFactoryInterface;

final readonly class McpServer implements McpServerInterface
{
    /**
     * @param iterable<McpMethodHandlerInterface> $handlers
     */
    public function __construct(
        private JsonRpcRequestFactoryInterface $requestFactory,
        private iterable $handlers,
    ) {
    }

    public function handle(string $requestPayload): JsonRpcMessageInterface
    {
        $message = $this->requestFactory->createFromString($requestPayload);
        if ($message instanceof JsonRpcErrorResponse) {
            return $message;
        }
        foreach ($this->handlers as $handler) {
            if ($handler->supports($message)) {
                return $handler->handle($message);
            }
        }

        return new JsonRpcErrorResponse(
            $message->getJsonRpc(),
            $message->getId(),
            new JsonRpcError(
                JsonRpcErrorCodeEnum::METHOD_NOT_FOUND,
                'Method not found or not supported',
            ),
        );
    }
}
