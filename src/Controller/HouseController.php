<?php

declare(strict_types=1);

namespace App\Controller;


use App\Repository\HouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HouseController extends AbstractController
{
    public function __construct(private readonly HouseRepository $houseRepository)
    {
    }


    #[Route('/api/houses', name: 'house_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $data = $this->getHouseData();
        return $this->json($data);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getHouseData(): array
    {

        $lines = $this->csvService->readAll($this->filename);
        $headers = array_shift($lines) ?? [];
        return array_map(fn(array $row) => array_combine($headers, $row), $lines);
    }
}
