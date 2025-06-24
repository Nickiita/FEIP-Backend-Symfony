<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\House;
use App\Repository\BookingRepository;
use App\Repository\HouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/bookings')]
class BookingController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly HouseRepository $houseRepository,
        private readonly BookingRepository $bookingRepository
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

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        [$houseId, $phone, $comment] = $this->extractBookingData($request);

        if (!is_numeric($houseId) || !$this->isValidPhone($phone)) {
            return $this->json(['error' => 'Invalid input'], HttpResponse::HTTP_BAD_REQUEST);
        }

        $house = $this->houseRepository->find($houseId);
        if (!$house) {
            return $this->json(['error' => 'House not found'], HttpResponse::HTTP_NOT_FOUND);
        }

        $booking = new Booking();
        $booking->setHouseId($house)
            ->setPhone($phone)
            ->setComment($comment);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $this->json(['status' => 'ok', 'id' => $booking->getId()], HttpResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        [$houseId, $phone, $comment] = $this->extractBookingData($request);

        if (!is_numeric($houseId) || !$this->isValidPhone($phone)) {
            return $this->json(['error' => 'Invalid input'], HttpResponse::HTTP_BAD_REQUEST);
        }

        $house = $this->houseRepository->find($houseId);
        if (!$house) {
            return $this->json(['error' => 'House not found'], HttpResponse::HTTP_NOT_FOUND);
        }

        $booking = $this->bookingRepository->find($id);
        if (!$booking) {
            return $this->json(['error' => 'Booking not found'], HttpResponse::HTTP_NOT_FOUND);
        }

        $booking->setHouseId($house)
            ->setPhone($phone)
            ->setComment($comment);

        $this->entityManager->flush();

        return $this->json(['status' => 'updated']);
    }
}
