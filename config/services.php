<?php

declare(strict_types=1);

use App\Capability\Tool\DistanceBetweenTowns;
use App\Capability\Tool\GetWeather;
use App\Mcp\McpMethodHandlerInterface;
use App\Mcp\McpServer;
use App\Mcp\MethodHandler\InitializeHandler;
use App\Mcp\MethodHandler\ToolsCallHandler;
use App\Mcp\MethodHandler\ToolsListHandler;
use App\Mcp\Registry\PromptsRegistry;
use App\Mcp\Registry\ResourcesRegistry;
use App\Mcp\Registry\ToolsRegistry;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->instanceof(
        McpMethodHandlerInterface::class
    )->tag('mcp.method_handler');

    $services->load('App\\', __DIR__ . '/../src/');

    $services->set(ToolsRegistry::class)
        ->arg('$capabilities', tagged_iterator('mcp.tool'))
        ->tag('mcp.capabilties_registry');

    $services->set(PromptsRegistry::class)
        ->arg('$capabilities', tagged_iterator('mcp.prompt'))
        ->tag('mcp.capabilties_registry');

    $services->set(ResourcesRegistry::class)
        ->arg('$capabilities', tagged_iterator('mcp.resource'))
        ->tag('mcp.capabilties_registry');

    $services->set(McpServer::class)->arg('$handlers', tagged_iterator('mcp.method_handler'));

    $services->set(InitializeHandler::class)
        ->args([
            '$name' => 'tagada',
            '$title' => 'tagada',
            '$version' => '1.0.0',
            '$protocolVersion' => '2025-06-18',
            '$registries' => tagged_iterator('mcp.capabilties_registry'),
        ]);

    $services->set(ToolsListHandler::class)
        ->arg('$toolsRegistry', service(ToolsRegistry::class));
    $services->set(ToolsCallHandler::class)
        ->arg('$toolsRegistry', service(ToolsRegistry::class));

    $services->set(
        GetWeather::class
    )->tag('mcp.tool');

    $services->set(
        DistanceBetweenTowns::class
    )->tag('mcp.tool');
};
