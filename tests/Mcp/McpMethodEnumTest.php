<?php

declare(strict_types=1);

namespace App\Tests\Mcp;

use App\Mcp\McpMethodEnum;
use PHPUnit\Framework\TestCase;

final class McpMethodEnumTest extends TestCase
{
    public function testAllMcpMethodsExist(): void
    {
        $this->assertSame('initialize', McpMethodEnum::INITIALIZE->value);
        $this->assertSame('tools/list', McpMethodEnum::TOOLS_LIST->value);
        $this->assertSame('tools/call', McpMethodEnum::TOOLS_CALL->value);
    }

    public function testTryFromWithValidValues(): void
    {
        $this->assertSame(McpMethodEnum::INITIALIZE, McpMethodEnum::tryFrom('initialize'));
        $this->assertSame(McpMethodEnum::TOOLS_LIST, McpMethodEnum::tryFrom('tools/list'));
        $this->assertSame(McpMethodEnum::TOOLS_CALL, McpMethodEnum::tryFrom('tools/call'));
        $this->assertSame(McpMethodEnum::PROMPTS_LIST, McpMethodEnum::tryFrom('prompts/list'));
        $this->assertSame(McpMethodEnum::PROMPTS_GET, McpMethodEnum::tryFrom('prompts/get'));
        $this->assertSame(McpMethodEnum::RESOURCES_LIST, McpMethodEnum::tryFrom('resources/list'));
        $this->assertSame(McpMethodEnum::RESOURCES_READ, McpMethodEnum::tryFrom('resources/read'));
    }

    public function testTryFromWithInvalidValueReturnsNull(): void
    {
        $this->assertNull(McpMethodEnum::tryFrom('invalid_method'));
        $this->assertNull(McpMethodEnum::tryFrom('unknown/method'));
        $this->assertNull(McpMethodEnum::tryFrom(''));
    }
}
