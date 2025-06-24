<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\CsvService;

#[Route('/api/houses', methods:['GET'])]
class HouseController extends AbstractController
{
    public function __construct(
        private readonly CsvService $csvService,
        private readonly string $filename
    ) {}

    public function __invoke(): JsonResponse
    {
        $lines = $this->csvService->readAll($this->filename);
        $headers = array_shift($lines) ?? [];
        $data = array_map(fn($row) => array_combine($headers, $row), $lines);
        return $this->json($data);
    }
}