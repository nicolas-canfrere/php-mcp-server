<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\Mcp\JsonRpc\JsonRpcErrorCodeEnum;
use App\Mcp\JsonRpc\JsonRpcErrorResponse;
use App\Mcp\JsonRpc\JsonRpcRequest;
use App\Mcp\JsonRpc\JsonRpcRequestFactory;
use App\Mcp\JsonRpc\JsonRpcResultResponse;
use App\Mcp\JsonRpc\JsonRpcVersionEnum;
use App\Mcp\McpMethodEnum;
use App\Mcp\McpMethodHandlerInterface;
use App\Mcp\McpServer;
use PHPUnit\Framework\TestCase;

final class McpServerTest extends TestCase
{
    public function testHandleDelegatesToCorrectHandler(): void
    {
        $handler = $this->createMock(McpMethodHandlerInterface::class);
        $handler->expects($this->once())
            ->method('supports')
            ->willReturn(true);

        $expectedResponse = new JsonRpcResultResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            ['status' => 'success'],
        );

        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($expectedResponse);

        $server = new McpServer(
            new JsonRpcRequestFactory(),
            [$handler],
        );

        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 1,
        ]);

        $result = $server->handle($payload);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandleWithJsonRpcErrorResponseFromFactory(): void
    {
        $handler = $this->createMock(McpMethodHandlerInterface::class);
        $handler->expects($this->never())
            ->method('supports');
        $handler->expects($this->never())
            ->method('handle');

        $server = new McpServer(
            new JsonRpcRequestFactory(),
            [$handler],
        );

        $payload = '{invalid json}';

        $result = $server->handle($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
        $this->assertSame(JsonRpcErrorCodeEnum::PARSE_ERROR, $result->getError()->getCode());
    }

    public function testHandleReturnsMethodNotFoundIfNoHandlerSupports(): void
    {
        $handler = $this->createMock(McpMethodHandlerInterface::class);
        $handler->expects($this->once())
            ->method('supports')
            ->willReturn(false);
        $handler->expects($this->never())
            ->method('handle');

        $server = new McpServer(
            new JsonRpcRequestFactory(),
            [$handler],
        );

        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 1,
        ]);

        $result = $server->handle($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
        $this->assertSame(JsonRpcErrorCodeEnum::METHOD_NOT_FOUND, $result->getError()->getCode());
        $this->assertSame('Method not found or not supported', $result->getError()->getMessage());
    }

    public function testHandleIteratesOverAllHandlers(): void
    {
        $handler1 = $this->createMock(McpMethodHandlerInterface::class);
        $handler1->expects($this->once())
            ->method('supports')
            ->willReturn(false);

        $handler2 = $this->createMock(McpMethodHandlerInterface::class);
        $handler2->expects($this->once())
            ->method('supports')
            ->willReturn(false);

        $handler3 = $this->createMock(McpMethodHandlerInterface::class);
        $handler3->expects($this->once())
            ->method('supports')
            ->willReturn(false);

        $server = new McpServer(
            new JsonRpcRequestFactory(),
            [$handler1, $handler2, $handler3],
        );

        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 1,
        ]);

        $result = $server->handle($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
    }

    public function testHandleStopsAtFirstSupportingHandler(): void
    {
        $handler1 = $this->createMock(McpMethodHandlerInterface::class);
        $handler1->expects($this->once())
            ->method('supports')
            ->willReturn(false);
        $handler1->expects($this->never())
            ->method('handle');

        $expectedResponse = new JsonRpcResultResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            ['status' => 'success'],
        );

        $handler2 = $this->createMock(McpMethodHandlerInterface::class);
        $handler2->expects($this->once())
            ->method('supports')
            ->willReturn(true);
        $handler2->expects($this->once())
            ->method('handle')
            ->willReturn($expectedResponse);

        $handler3 = $this->createMock(McpMethodHandlerInterface::class);
        $handler3->expects($this->never())
            ->method('supports');
        $handler3->expects($this->never())
            ->method('handle');

        $server = new McpServer(
            new JsonRpcRequestFactory(),
            [$handler1, $handler2, $handler3],
        );

        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 1,
        ]);

        $result = $server->handle($payload);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandleWithMultipleHandlersConfigured(): void
    {
        $initializeHandler = $this->createMock(McpMethodHandlerInterface::class);
        $initializeHandler->expects($this->once())
            ->method('supports')
            ->willReturnCallback(function (JsonRpcRequest $request) {
                return McpMethodEnum::INITIALIZE === $request->getMethod();
            });
        $initializeHandler->expects($this->once())
            ->method('handle')
            ->willReturn(new JsonRpcResultResponse(
                JsonRpcVersionEnum::VERSION_2,
                1,
                ['initialized' => true],
            ));

        $toolsListHandler = $this->createMock(McpMethodHandlerInterface::class);
        $toolsListHandler->expects($this->never())
            ->method('supports');
        $toolsListHandler->expects($this->never())
            ->method('handle');

        $server = new McpServer(
            new JsonRpcRequestFactory(),
            [$initializeHandler, $toolsListHandler],
        );

        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 1,
        ]);

        $result = $server->handle($payload);

        $this->assertInstanceOf(JsonRpcResultResponse::class, $result);
        $this->assertSame(['initialized' => true], $result->getResult());
    }
}
