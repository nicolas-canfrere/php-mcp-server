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
        if (!isset($arguments['location']) || !is_string($arguments['location'])) {
            throw new \InvalidArgumentException('location must be a string');
        }

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

    /**
     * @param array{latitude: float, longitude: float} $latLong
     *
     * @return array<mixed>
     */
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

    /**
     * @return array{latitude: float, longitude: float}
     */
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
        if (empty($result['results']) || !is_array($result['results'])) {
            throw new \Exception(sprintf('Town "%s" not found', $townName));
        }

        $firstResult = $result['results'][0];
        if (!is_array($firstResult) || !isset($firstResult['latitude'], $firstResult['longitude'])) {
            throw new \Exception(sprintf('Invalid response format for town "%s"', $townName));
        }

        if (!is_numeric($firstResult['latitude']) || !is_numeric($firstResult['longitude'])) {
            throw new \Exception(sprintf('Invalid latitude/longitude format for town "%s"', $townName));
        }

        return [
            'latitude' => (float) $firstResult['latitude'],
            'longitude' => (float) $firstResult['longitude'],
        ];
    }
}
