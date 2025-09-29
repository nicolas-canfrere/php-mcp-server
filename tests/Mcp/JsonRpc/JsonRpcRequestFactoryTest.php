<?php

declare(strict_types=1);

namespace App\Tests\Mcp\JsonRpc;

use App\Mcp\JsonRpc\JsonRpcErrorCodeEnum;
use App\Mcp\JsonRpc\JsonRpcErrorResponse;
use App\Mcp\JsonRpc\JsonRpcRequest;
use App\Mcp\JsonRpc\JsonRpcRequestFactory;
use App\Mcp\McpMethodEnum;
use PHPUnit\Framework\TestCase;

final class JsonRpcRequestFactoryTest extends TestCase
{
    private JsonRpcRequestFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new JsonRpcRequestFactory();
    }

    public function testCreateValidRequestWithAllParameters(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 1,
            'params' => ['key' => 'value'],
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcRequest::class, $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame(McpMethodEnum::INITIALIZE, $result->getMethod());
        $this->assertSame(['key' => 'value'], $result->getParams());
    }

    public function testCreateValidRequestWithoutOptionalParameters(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcRequest::class, $result);
        $this->assertNull($result->getId());
        $this->assertSame([], $result->getParams());
    }

    public function testCreateRequestWithStringId(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 'string-id',
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcRequest::class, $result);
        $this->assertSame('string-id', $result->getId());
    }

    public function testCreateRequestWithIntegerId(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => 42,
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcRequest::class, $result);
        $this->assertSame(42, $result->getId());
    }

    public function testCreateRequestWithNullId(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => null,
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcRequest::class, $result);
        $this->assertNull($result->getId());
    }

    public function testErrorParseErrorWithInvalidJson(): void
    {
        $payload = '{invalid json}';

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
        $this->assertSame(JsonRpcErrorCodeEnum::PARSE_ERROR, $result->getError()->getCode());
    }

    public function testErrorInvalidRequestWithMissingJsonRpc(): void
    {
        $payload = json_encode([
            'method' => 'initialize',
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
        $this->assertSame(JsonRpcErrorCodeEnum::INVALID_REQUEST, $result->getError()->getCode());
        $this->assertSame('Invalid JsonRpc version.', $result->getError()->getMessage());
    }

    public function testErrorInvalidRequestWithInvalidJsonRpc(): void
    {
        $payload = json_encode([
            'jsonrpc' => '1.0',
            'method' => 'initialize',
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
        $this->assertSame(JsonRpcErrorCodeEnum::INVALID_REQUEST, $result->getError()->getCode());
        $this->assertSame('Invalid JsonRpc version.', $result->getError()->getMessage());
    }

    public function testErrorInvalidRequestWithMissingMethod(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
        $this->assertSame(JsonRpcErrorCodeEnum::INVALID_REQUEST, $result->getError()->getCode());
        $this->assertSame('Method name not set.', $result->getError()->getMessage());
    }

    public function testErrorInvalidRequestWithNonStringMethod(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 123,
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
        $this->assertSame(JsonRpcErrorCodeEnum::INVALID_REQUEST, $result->getError()->getCode());
        $this->assertSame('Method name not set.', $result->getError()->getMessage());
    }

    public function testErrorMethodNotFoundWithUnknownMethod(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'unknown_method',
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
        $this->assertSame(JsonRpcErrorCodeEnum::METHOD_NOT_FOUND, $result->getError()->getCode());
        $this->assertSame('Method not found.', $result->getError()->getMessage());
    }

    public function testErrorInvalidRequestWithInvalidIdType(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'id' => ['invalid'],
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcErrorResponse::class, $result);
        $this->assertSame(JsonRpcErrorCodeEnum::INVALID_REQUEST, $result->getError()->getCode());
        $this->assertSame('Id must be integer or string or null.', $result->getError()->getMessage());
    }

    public function testHandleOptionalParametersCorrectly(): void
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'tools/call',
            'params' => [
                'name' => 'tool_name',
                'arguments' => ['arg1' => 'value1'],
            ],
        ]);

        $result = $this->factory->createFromString($payload);

        $this->assertInstanceOf(JsonRpcRequest::class, $result);
        $this->assertSame([
            'name' => 'tool_name',
            'arguments' => ['arg1' => 'value1'],
        ], $result->getParams());
    }
}
