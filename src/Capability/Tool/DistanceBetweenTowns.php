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
        $town1 = $arguments['town_1'];
        $town2 = $arguments['town_2'];
        $latLongTown1 = $this->getLatLong($town1);
        $latLongTown2 = $this->getLatLong($town2);
        $distanceKm = $this->getDistance($latLongTown1, $latLongTown2);

        return [
            'content' => [
                ['type'=>'text', 'text'=> json_encode(['distance' => sprintf('%d km', $distanceKm)])],
            ]
        ];
    }

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
                ]
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
}
