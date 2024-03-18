<?php

namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    
    

    #[ORM\Column(nullable: true)]
    private ?int $quantiteVendue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $montantTotal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    
    
    
    
    

    
    
    
    
    
    

    public function getQuantiteVendue(): ?int
    {
        return $this->quantiteVendue;
    }

    public function setQuantiteVendue(?int $quantiteVendue): static
    {
        $this->quantiteVendue = $quantiteVendue;

        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(?string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }
}
