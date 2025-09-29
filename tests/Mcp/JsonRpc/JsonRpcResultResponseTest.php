<?php

declare(strict_types=1);

namespace App\Tests\Mcp\JsonRpc;

use App\Mcp\JsonRpc\JsonRpcResultResponse;
use App\Mcp\JsonRpc\JsonRpcVersionEnum;
use PHPUnit\Framework\TestCase;

final class JsonRpcResultResponseTest extends TestCase
{
    public function testGetters(): void
    {
        $response = new JsonRpcResultResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            ['status' => 'success'],
        );

        $this->assertSame(JsonRpcVersionEnum::VERSION_2, $response->getJsonRpc());
        $this->assertSame(1, $response->getId());
        $this->assertSame(['status' => 'success'], $response->getResult());
    }

    public function testJsonSerializeReturnsCorrectStructure(): void
    {
        $response = new JsonRpcResultResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            ['status' => 'success'],
        );

        $serialized = $response->jsonSerialize();

        $this->assertSame('2.0', $serialized['jsonrpc']);
        $this->assertSame(1, $serialized['id']);
        $this->assertSame(['status' => 'success'], $serialized['result']);
    }

    public function testJsonSerializeWithEmptyResult(): void
    {
        $response = new JsonRpcResultResponse(
            JsonRpcVersionEnum::VERSION_2,
            1,
            [],
        );

        $serialized = $response->jsonSerialize();

        $this->assertSame([], $serialized['result']);
    }

    public function testJsonSerializeWithComplexResult(): void
    {
        $complexResult = [
            'protocolVersion' => '2024-11-05',
            'serverInfo' => [
                'name' => 'test-server',
                'version' => '1.0.0',
            ],
            'capabilities' => [
                'tools' => ['listChanged' => false],
            ],
        ];

        $response = new JsonRpcResultResponse(
            JsonRpcVersionEnum::VERSION_2,
            'string-id',
            $complexResult,
        );

        $serialized = $response->jsonSerialize();

        $this->assertSame('2.0', $serialized['jsonrpc']);
        $this->assertSame('string-id', $serialized['id']);
        $this->assertSame($complexResult, $serialized['result']);
    }
}
