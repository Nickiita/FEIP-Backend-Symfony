<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\CsvService;

#[Route('/api/houses', methods: ['GET'])]
class HouseControllerTest extends AbstractController
{
    public function __construct(
        private readonly CsvService $csvService,
        private readonly string $filename
    ) {}

    public function __invoke(): JsonResponse
    {

        $lines = $this->csvService->readAll($this->filename);

        $headers = array_shift($lines) ?? [];

        $data = [];
        foreach ($lines as $row) {
            if (count($row) !== count($headers)) {
                continue;
            }
            $data[] = array_combine($headers, $row);
        }
        return $this->json($data);
    }
}