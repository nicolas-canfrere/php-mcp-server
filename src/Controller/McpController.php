<?php

declare(strict_types=1);

namespace App\Controller;

use App\Mcp\McpServerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class McpController
{
    public function __construct(
        private McpServerInterface $server,
    ) {
    }

    #[Route('/mcp', name: 'mcp', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $response = $this->server->handle($request->getContent());

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
