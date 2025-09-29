<?php

declare(strict_types=1);

namespace App\Tests\Mcp\JsonRpc;

use App\Mcp\JsonRpc\JsonRpcError;
use App\Mcp\JsonRpc\JsonRpcErrorCodeEnum;
use App\Mcp\JsonRpc\JsonRpcErrorResponse;
use App\Mcp\JsonRpc\JsonRpcVersionEnum;
use PHPUnit\Framework\TestCase;

final class JsonRpcErrorResponseTest extends TestCase
{
    public function testGetters(): void
    {
        $error = new JsonRpcError(
            JsonRpcErrorCodeEnum::INTERNAL_ERROR,
            'Internal error',
        );

        $response = new JsonRpcErrorResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            $error,
        );

        $this->assertSame(JsonRpcVersionEnum::VERSION_2, $response->getJsonRpc());
        $this->assertSame(1, $response->getId());
        $this->assertSame($error, $response->getError());
    }

    public function testJsonSerializeReturnsCorrectStructure(): void
    {
        $error = new JsonRpcError(
            JsonRpcErrorCodeEnum::INVALID_REQUEST,
            'Invalid request',
        );

        $response = new JsonRpcErrorResponse(
            JsonRpcVersionEnum::VERSION_2,
            'string-id',
            $error,
        );

        $serialized = $response->jsonSerialize();

        $this->assertSame('2.0', $serialized['jsonrpc']);
        $this->assertSame('string-id', $serialized['id']);
        $this->assertSame($error, $serialized['error']);
    }

    public function testJsonSerializeWithDifferentErrorTypes(): void
    {
        $parseError = new JsonRpcError(
            JsonRpcErrorCodeEnum::PARSE_ERROR,
            'Parse error',
        );

        $methodNotFoundError = new JsonRpcError(
            JsonRpcErrorCodeEnum::METHOD_NOT_FOUND,
            'Method not found',
        );

        $invalidParamsError = new JsonRpcError(
            JsonRpcErrorCodeEnum::INVALID_PARAMS,
            'Invalid params',
        );

        $responseParseError = new JsonRpcErrorResponse(
            JsonRpcVersionEnum::VERSION_2,
            null,
            $parseError,
        );

        $responseMethodNotFound = new JsonRpcErrorResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            $methodNotFoundError,
        );

        $responseInvalidParams = new JsonRpcErrorResponse(
            JsonRpcVersionEnum::VERSION_2,
            2,
            $invalidParamsError,
        );

        $this->assertSame($parseError, $responseParseError->getError());
        $this->assertSame($methodNotFoundError, $responseMethodNotFound->getError());
        $this->assertSame($invalidParamsError, $responseInvalidParams->getError());
    }
}
