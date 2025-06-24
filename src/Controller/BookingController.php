<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use App\Service\CsvService;

#[Route('/api/bookings')]
class BookingController extends AbstractController
{
    public function __construct(
        private readonly CsvService $csvService,
        private readonly string $filename,
        private readonly string $housesFilename
    ) {}

    private function extractBookingData(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        return [
            $data['houseId'] ?? null,
            $data['phone'] ?? '',
            $data['comment'] ?? '',
        ];
    }

    private function isValidPhone(string $phone): bool
    {
        return strlen($phone) <= 16 && preg_match('/^[0-9+\- ]+$/', $phone);
    }

    private function houseExists(int $houseId): bool
    {
        $houses = $this->csvService->readAll($this->housesFilename);
        foreach ($houses as $house) {
            if ((int)$house[0] === $houseId) {
                return true;
            }
        }
        return false;
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        [$houseId, $phone, $comment] = $this->extractBookingData($request);

        if (!is_numeric($houseId) || !$this->isValidPhone($phone)) {
            return $this->json(['error' => 'Invalid input'], HttpResponse::HTTP_BAD_REQUEST);
        }

        if (!$this->houseExists((int)$houseId)) {
            return $this->json(['error' => 'House not found'], HttpResponse::HTTP_NOT_FOUND);
        }

        $rows = $this->csvService->readAll($this->filename);
        $nextId = count($rows);

        $row = [
            $nextId,
            (int)$houseId,
            $phone,
            $comment
        ];

        $this->csvService->append($this->filename, $row);

        return $this->json(['status' => 'ok', 'id' => $nextId], HttpResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        [$houseId, $phone, $comment] = $this->extractBookingData($request);

        if (!is_numeric($houseId) || !$this->isValidPhone($phone)) {
            return $this->json(['error' => 'Invalid input'], HttpResponse::HTTP_BAD_REQUEST);
        }

        if (!$this->houseExists((int)$houseId)) {
            return $this->json(['error' => 'House not found'], HttpResponse::HTTP_NOT_FOUND);
        }

        $row = [
            $id,
            (int)$houseId,
            $phone,
            $comment
        ];

        try {
            $this->csvService->overwriteRow($this->filename, $id, $row);
            return $this->json(['status' => 'updated']);
        } catch (\RuntimeException $e) {
            return $this->json(['error' => $e->getMessage()], HttpResponse::HTTP_NOT_FOUND);
        }
    }
}