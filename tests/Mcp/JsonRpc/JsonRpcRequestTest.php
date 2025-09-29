<?php

declare(strict_types=1);

namespace App\Tests\Mcp\JsonRpc;

use App\Mcp\JsonRpc\JsonRpcRequest;
use App\Mcp\JsonRpc\JsonRpcVersionEnum;
use App\Mcp\McpMethodEnum;
use PHPUnit\Framework\TestCase;

final class JsonRpcRequestTest extends TestCase
{
    public function testGetters(): void
    {
        $request = new JsonRpcRequest(
            JsonRpcVersionEnum::VERSION_2,
            1,
            McpMethodEnum::INITIALIZE,
            ['key' => 'value'],
        );

        $this->assertSame(JsonRpcVersionEnum::VERSION_2, $request->getJsonRpc());
        $this->assertSame(1, $request->getId());
        $this->assertSame(McpMethodEnum::INITIALIZE, $request->getMethod());
        $this->assertSame(['key' => 'value'], $request->getParams());
    }

    public function testIsNotificationReturnsTrueWhenIdIsNull(): void
    {
        $request = new JsonRpcRequest(
            JsonRpcVersionEnum::VERSION_2,
            null,
            McpMethodEnum::INITIALIZE,
            [],
        );

        $this->assertTrue($request->isNotification());
    }

    public function testIsNotificationReturnsFalseWhenIdExists(): void
    {
        $request = new JsonRpcRequest(
            JsonRpcVersionEnum::VERSION_2,
            1,
            McpMethodEnum::INITIALIZE,
            [],
        );

        $this->assertFalse($request->isNotification());
    }

    public function testConstructionWithDifferentIdTypes(): void
    {
        $requestWithIntId = new JsonRpcRequest(
            JsonRpcVersionEnum::VERSION_2,
            42,
            McpMethodEnum::INITIALIZE,
            [],
        );
        $this->assertSame(42, $requestWithIntId->getId());

        $requestWithStringId = new JsonRpcRequest(
            JsonRpcVersionEnum::VERSION_2,
            'string-id',
            McpMethodEnum::INITIALIZE,
            [],
        );
        $this->assertSame('string-id', $requestWithStringId->getId());

        $requestWithNullId = new JsonRpcRequest(
            JsonRpcVersionEnum::VERSION_2,
            null,
            McpMethodEnum::INITIALIZE,
            [],
        );
        $this->assertNull($requestWithNullId->getId());
    }
}
