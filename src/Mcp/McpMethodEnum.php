<?php

declare(strict_types=1);

namespace App\Mcp;

enum McpMethodEnum: string
{
    case INITIALIZE = 'initialize';
    case TOOLS_LIST = 'tools/list';
    case TOOLS_CALL = 'tools/call';
    case NOTIFICATION_TOOLS_LIST_CHANGED = 'notifications/tools/list_changed';
    case PROMPTS_LIST = 'prompts/list';
    case PROMPTS_GET = 'prompts/get';
    case RESOURCES_LIST = 'resources/list';
    case RESOURCES_TEMPLATE_LIST = 'resources/templates/list';
    case RESOURCES_READ = 'resources/read';
    case RESOURCES_SUBSCRIBE = 'resources/subscribe';
    case NOTIFICATION_RESOURCES_UPDATED = 'notifications/resources/updated';
    case NOTIFICATION_RESOURCES_LIST_CHANGED = 'notifications/resources/list_changed';
}
