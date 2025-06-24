<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use App\Service\CsvService;

#[Route('/api/bookings')]
class BookingControllerTest extends AbstractController
{
    public function __construct(
        private readonly CsvService $csvService,
        private readonly string $filename,
        private readonly string $housesFilename
    ) {}

    private function extractBookingData(Request $request): array
    {
        $data = json_decode($request->getContent(), true) ?: [];
        return [
            $data['houseId'] ?? null,
            $data['phone'] ?? '',
            $data['comment'] ?? '',
        ];
    }

    private function isValidPhone(string $phone): bool
    {
        return preg_match('/^[0-9+\- ]{1,16}$/', $phone) === 1;
    }

    private function houseExists(int $houseId): bool
    {
        $houses = $this->csvService->readAll($this->housesFilename);
        foreach ($houses as $row) {
            if (isset($row[0]) && (int)$row[0] === $houseId) {
                return true;
            }
        }
        return false;
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        [$houseId, $phone, $comment] = $this->extractBookingData($request);

        if (!is_numeric($houseId) || !$this->isValidPhone((string)$phone)) {
            return $this->json(['error' => 'Invalid input'], HttpResponse::HTTP_BAD_REQUEST);
        }

        $houseId = (int)$houseId;
        if (!$this->houseExists($houseId)) {
            return $this->json(['error' => 'House not found'], HttpResponse::HTTP_NOT_FOUND);
        }

        $rows = $this->csvService->readAll($this->filename);
        $nextId = count($rows);

        $row = [
            $nextId,
            $houseId,
            (string)$phone,
            (string)$comment,
        ];

        $this->csvService->append($this->filename, $row);

        return $this->json(['status' => 'ok', 'id' => $nextId], HttpResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        [$houseId, $phone, $comment] = $this->extractBookingData($request);

        if (!is_numeric($houseId) || !$this->isValidPhone((string)$phone)) {
            return $this->json(['error' => 'Invalid input'], HttpResponse::HTTP_BAD_REQUEST);
        }

        $houseId = (int)$houseId;
        if (!$this->houseExists($houseId)) {
            return $this->json(['error' => 'House not found'], HttpResponse::HTTP_NOT_FOUND);
        }

        $row = [
            $id,
            $houseId,
            (string)$phone,
            (string)$comment,
        ];

        try {
            $this->csvService->overwriteRow($this->filename, $id, $row);
            return $this->json(['status' => 'updated']);
        } catch (\RuntimeException $e) {
            return $this->json(['error' => $e->getMessage()], HttpResponse::HTTP_NOT_FOUND);
        }
    }
}