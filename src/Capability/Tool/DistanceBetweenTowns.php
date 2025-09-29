<?php

declare(strict_types=1);

namespace App\Capability\Tool;

use App\Mcp\ToolCapabilityInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DistanceBetweenTowns implements ToolCapabilityInterface
{
    public function __construct(
        private readonly HttpClientInterface $geoLocationClient,
    ) {
    }

    public function handle(array $arguments): array
    {
        if (!isset($arguments['town_1']) || !is_string($arguments['town_1'])) {
            throw new \InvalidArgumentException('town_1 must be a string');
        }
        if (!isset($arguments['town_2']) || !is_string($arguments['town_2'])) {
            throw new \InvalidArgumentException('town_2 must be a string');
        }

        $town1 = $arguments['town_1'];
        $town2 = $arguments['town_2'];
        $latLongTown1 = $this->getLatLong($town1);
        $latLongTown2 = $this->getLatLong($town2);
        $distanceKm = $this->getDistance($latLongTown1, $latLongTown2);

        return [
            'content' => [
                ['type' => 'text', 'text' => json_encode(['distance' => sprintf('%d km', $distanceKm)])],
            ],
        ];
    }

    public function getDefinition(): array
    {
        return [
            'name' => $this->getName(),
            'title' => 'Get the distance between 2 towns',
            'description' => 'Get the distance in km between 2 towns.',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'town_1' => [
                        'type' => 'string',
                        'description' => 'City name of town 1 or its zip code',
                    ],
                    'town_2' => [
                        'type' => 'string',
                        'description' => 'City name of town 2 or its zip code',
                    ],
                ],
                'required' => ['town_1', 'town_2'],
            ],
        ];
    }

    public function getName(): string
    {
        return 'Distance_between_towns';
    }

    /**
     * @param array{latitude: float, longitude: float} $latLongTown1
     * @param array{latitude: float, longitude: float} $latLongTown2
     */
    private function getDistance(array $latLongTown1, array $latLongTown2): int
    {
        $earthRadiusKm = 6371;
        $latitudeDelta = deg2rad($latLongTown2['latitude'] - $latLongTown1['latitude']);
        $longitudeDelta = deg2rad($latLongTown2['longitude'] - $latLongTown1['longitude']);
        $haversineA = sin($latitudeDelta / 2) * sin($latitudeDelta / 2) +
            cos(deg2rad($latLongTown1['latitude'])) * cos(deg2rad($latLongTown2['latitude'])) *
            sin($longitudeDelta / 2) * sin($longitudeDelta / 2);
        $haversineC = 2 * atan2(sqrt($haversineA), sqrt(1 - $haversineA));
        $distanceKm = $earthRadiusKm * $haversineC;

        return (int) round($distanceKm);
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
