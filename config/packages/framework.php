<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'secret' => '%env(APP_SECRET)%',
        'session' => true,
        'http_client' => [
            'scoped_clients' => [
                'geo_location.client' => [
                    'base_uri' => 'https://geocoding-api.open-meteo.com/v1/search',
                ],
                'weather.client' => [
                    'base_uri' => 'https://api.open-meteo.com/v1/forecast',
                ]
            ]
        ]
    ]);
    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('framework', [
            'test' => true,
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);
    }
};
