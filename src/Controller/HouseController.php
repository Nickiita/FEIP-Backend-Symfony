<?php

namespace App\Controller;

use App\Repository\HouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/houses', methods: ['GET'])]
class HouseController extends AbstractController
{
    public function __construct(private readonly HouseRepository $houseRepository) {}

    public function __invoke(): JsonResponse
    {
        $houses = $this->houseRepository->findAll();

        $data = array_map(function ($house) {
            return [
                'id' => $house->getId(),
                'quantitySingleBeds' => $house->getQuantitySingleBeds(),
                'quantityDoubleBeds' => $house->getQuantityDoubleBeds(),
                'seaDistance' => $house->getSeaDistance(),
                'isAvailable' => $house->isAvailable(),
                'pricePerNight' => $house->getPricePerNight(),
                'quantityParkingSpaces' => $house->getQuantityParkingSpaces(),
                'hasShower' => $house->hasShower(),
                'hasKitchen' => $house->hasKitchen(),
                'hasTerrace' => $house->hasTerrace(),
                'hasAC' => $house->hasAC(),
            ];
        }, $houses);

        return $this->json($data);
    }
}
