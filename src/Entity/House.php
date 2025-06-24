<?php

namespace App\Entity;

use App\Repository\HouseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseRepository::class)]
class House
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantitySingleBeds = null;

    #[ORM\Column]
    private ?int $quantityDoubleBeds = null;

    #[ORM\Column]
    private ?int $seaDistance = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    #[ORM\Column]
    private ?float $pricePerNight = null;

    #[ORM\Column]
    private ?int $quantityParkingSpaces = null;

    #[ORM\Column]
    private ?bool $hasShower = null;

    #[ORM\Column]
    private ?bool $hasKitchen = null;

    #[ORM\Column]
    private ?bool $hasTerrace = null;

    #[ORM\Column]
    private ?bool $hasAC = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantitySingleBeds(): ?int
    {
        return $this->quantitySingleBeds;
    }

    public function setQuantitySingleBeds(int $quantitySingleBeds): static
    {
        $this->quantitySingleBeds = $quantitySingleBeds;

        return $this;
    }

    public function getQuantityDoubleBeds(): ?int
    {
        return $this->quantityDoubleBeds;
    }

    public function setQuantityDoubleBeds(int $quantityDoubleBeds): static
    {
        $this->quantityDoubleBeds = $quantityDoubleBeds;

        return $this;
    }

    public function getSeaDistance(): ?int
    {
        return $this->seaDistance;
    }

    public function setSeaDistance(int $seaDistance): static
    {
        $this->seaDistance = $seaDistance;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getPricePerNight(): ?float
    {
        return $this->pricePerNight;
    }

    public function setPricePerNight(float $pricePerNight): static
    {
        $this->pricePerNight = $pricePerNight;

        return $this;
    }

    public function getQuantityParkingSpaces(): ?int
    {
        return $this->quantityParkingSpaces;
    }

    public function setQuantityParkingSpaces(int $quantityParkingSpaces): static
    {
        $this->quantityParkingSpaces = $quantityParkingSpaces;

        return $this;
    }

    public function hasShower(): ?bool
    {
        return $this->hasShower;
    }

    public function setHasShower(bool $hasShower): static
    {
        $this->hasShower = $hasShower;

        return $this;
    }

    public function hasKitchen(): ?bool
    {
        return $this->hasKitchen;
    }

    public function setHasKitchen(bool $hasKitchen): static
    {
        $this->hasKitchen = $hasKitchen;

        return $this;
    }

    public function hasTerrace(): ?bool
    {
        return $this->hasTerrace;
    }

    public function setHasTerrace(bool $hasTerrace): static
    {
        $this->hasTerrace = $hasTerrace;

        return $this;
    }

    public function hasAC(): ?bool
    {
        return $this->hasAC;
    }

    public function setHasAC(bool $hasAC): static
    {
        $this->hasAC = $hasAC;

        return $this;
    }
}
