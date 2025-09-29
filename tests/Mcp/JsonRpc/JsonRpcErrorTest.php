<?php

declare(strict_types=1);

namespace App\Tests\Mcp\JsonRpc;

use App\Mcp\JsonRpc\JsonRpcError;
use App\Mcp\JsonRpc\JsonRpcErrorCodeEnum;
use PHPUnit\Framework\TestCase;

final class JsonRpcErrorTest extends TestCase
{
    public function testGetters(): void
    {
        $error = new JsonRpcError(
            JsonRpcErrorCodeEnum::INTERNAL_ERROR,
            'Internal error',
            ['detail' => 'Additional information'],
        );

        $this->assertSame(JsonRpcErrorCodeEnum::INTERNAL_ERROR, $error->getCode());
        $this->assertSame('Internal error', $error->getMessage());
        $this->assertSame(['detail' => 'Additional information'], $error->getData());
    }

    public function testJsonSerializeWithData(): void
    {
        $error = new JsonRpcError(
            JsonRpcErrorCodeEnum::INVALID_PARAMS,
            'Invalid parameters',
            ['expected' => 'string', 'received' => 'int'],
        );

        $serialized = $error->jsonSerialize();

        $this->assertSame(-32602, $serialized['code']);
        $this->assertSame('Invalid parameters', $serialized['message']);
        $this->assertArrayHasKey('data', $serialized);
        $this->assertSame(['expected' => 'string', 'received' => 'int'], $serialized['data']);
    }

    public function testJsonSerializeWithoutData(): void
    {
        $error = new JsonRpcError(
            JsonRpcErrorCodeEnum::METHOD_NOT_FOUND,
            'Method not found',
        );

        $serialized = $error->jsonSerialize();

        $this->assertSame(-32601, $serialized['code']);
        $this->assertSame('Method not found', $serialized['message']);
        $this->assertArrayNotHasKey('data', $serialized);
    }

    public function testConstructionWithEachErrorCode(): void
    {
        $parseError = new JsonRpcError(
            JsonRpcErrorCodeEnum::PARSE_ERROR,
            'Parse error',
        );
        $this->assertSame(-32700, $parseError->getCode()->value);

        $invalidRequest = new JsonRpcError(
            JsonRpcErrorCodeEnum::INVALID_REQUEST,
            'Invalid request',
        );
        $this->assertSame(-32600, $invalidRequest->getCode()->value);

        $methodNotFound = new JsonRpcError(
            JsonRpcErrorCodeEnum::METHOD_NOT_FOUND,
            'Method not found',
        );
        $this->assertSame(-32601, $methodNotFound->getCode()->value);

        $invalidParams = new JsonRpcError(
            JsonRpcErrorCodeEnum::INVALID_PARAMS,
            'Invalid params',
        );
        $this->assertSame(-32602, $invalidParams->getCode()->value);

        $internalError = new JsonRpcError(
            JsonRpcErrorCodeEnum::INTERNAL_ERROR,
            'Internal error',
        );
        $this->assertSame(-32603, $internalError->getCode()->value);
    }
}
