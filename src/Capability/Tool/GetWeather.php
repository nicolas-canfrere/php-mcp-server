<?php

declare(strict_types=1);

namespace App\Capability\Tool;

use App\Mcp\ToolCapabilityInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GetWeather implements ToolCapabilityInterface
{
    public function __construct(
        private readonly HttpClientInterface $geoLocationClient,
        private readonly HttpClientInterface $weatherClient,
    ) {
    }

    public function handle(array $arguments): array
    {
        $location = $arguments['location'];
        $latLongLocation = $this->getLatLong($location);
        $weather = $this->getWeather($latLongLocation);

        return ['content' => [['type' => 'text', 'text' => json_encode($weather, JSON_THROW_ON_ERROR)]]];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefinition(): array
    {
        return [
            'name' => $this->getName(),
            'title' => 'Get Weather',
            'description' => 'Get current weather information for a location',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'location' => [
                        'type' => 'string',
                        'description' => 'City name or zip code',
                    ],
                ],
                'required' => ['location'],
            ],
        ];
    }

    public function getName(): string
    {
        return 'get_weather';
    }

    private function getWeather(array $latLong): array
    {
        $response = $this->weatherClient->request(
            'GET',
            '',
            [
                'query' => [
                    'latitude' => $latLong['latitude'],
                    'longitude' => $latLong['longitude'],
                    'daily' => 'temperature_2m_max,temperature_2m_min',
                    'forecast_days' => 2,
                ],
            ]
        );

        return $response->toArray();
    }

    private function getLatLong(string $townName): array
    {
        $response = $this->geoLocationClient->request(
            'GET',
            '',
            [
                'query' => [
                    'name' => $townName,
                    'count' => 1,
                    'language' => 'fr',
                ],
            ]
        );
        $result = $response->toArray();
        if (empty($result['results'])) {
            throw new \Exception(sprintf('Town "%s" not found', $townName));
        }

        return [
            'latitude' => $result['results'][0]['latitude'],
            'longitude' => $result['results'][0]['longitude'],
        ];
    }
}
