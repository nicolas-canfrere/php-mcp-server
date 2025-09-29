<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\McpController;
use App\Mcp\JsonRpc\JsonRpcErrorResponse;
use App\Mcp\JsonRpc\JsonRpcResultResponse;
use App\Mcp\JsonRpc\JsonRpcVersionEnum;
use App\Mcp\McpServerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class McpControllerTest extends TestCase
{
    public function testInvokeCallsServerHandleWithRequestContent(): void
    {
        $requestContent = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 1,
        ]);

        $expectedResponse = new JsonRpcResultResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            ['status' => 'initialized'],
        );

        $server = $this->createMock(McpServerInterface::class);
        $server->expects($this->once())
            ->method('handle')
            ->with($requestContent)
            ->willReturn($expectedResponse);

        $controller = new McpController($server);

        $request = new Request([], [], [], [], [], [], $requestContent);

        $response = $controller($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testInvokeReturnsJsonResponseWithStatus200(): void
    {
        $requestContent = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 1,
        ]);

        $expectedResponse = new JsonRpcResultResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            ['status' => 'initialized'],
        );

        $server = $this->createMock(McpServerInterface::class);
        $server->method('handle')
            ->willReturn($expectedResponse);

        $controller = new McpController($server);

        $request = new Request([], [], [], [], [], [], $requestContent);

        $response = $controller($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testInvokeSerializesResponseCorrectly(): void
    {
        $requestContent = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 1,
        ]);

        $expectedResponse = new JsonRpcResultResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            ['protocolVersion' => '2024-11-05'],
        );

        $server = $this->createMock(McpServerInterface::class);
        $server->method('handle')
            ->willReturn($expectedResponse);

        $controller = new McpController($server);

        $request = new Request([], [], [], [], [], [], $requestContent);

        $response = $controller($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('2.0', $responseData['jsonrpc']);
        $this->assertSame(1, $responseData['id']);
        $this->assertSame(['protocolVersion' => '2024-11-05'], $responseData['result']);
    }

    public function testInvokeWithEmptyRequest(): void
    {
        $requestContent = '';

        $errorResponse = new JsonRpcErrorResponse(
            JsonRpcVersionEnum::VERSION_2,
            null,
            new \App\Mcp\JsonRpc\JsonRpcError(
                \App\Mcp\JsonRpc\JsonRpcErrorCodeEnum::PARSE_ERROR,
                'Empty request',
            ),
        );

        $server = $this->createMock(McpServerInterface::class);
        $server->expects($this->once())
            ->method('handle')
            ->with($requestContent)
            ->willReturn($errorResponse);

        $controller = new McpController($server);

        $request = new Request([], [], [], [], [], [], $requestContent);

        $response = $controller($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testInvokeWithInvalidRequest(): void
    {
        $requestContent = '{invalid json}';

        $errorResponse = new JsonRpcErrorResponse(
            JsonRpcVersionEnum::VERSION_2,
            null,
            new \App\Mcp\JsonRpc\JsonRpcError(
                \App\Mcp\JsonRpc\JsonRpcErrorCodeEnum::PARSE_ERROR,
                'Invalid JSON',
            ),
        );

        $server = $this->createMock(McpServerInterface::class);
        $server->expects($this->once())
            ->method('handle')
            ->with($requestContent)
            ->willReturn($errorResponse);

        $controller = new McpController($server);

        $request = new Request([], [], [], [], [], [], $requestContent);

        $response = $controller($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
