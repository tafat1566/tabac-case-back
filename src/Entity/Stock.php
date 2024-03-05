<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?Produit $produitt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getProduitt(): ?Produit
    {
        return $this->produitt;
    }

    public function setProduitt(?Produit $produitt): static
    {
        $this->produitt = $produitt;

        return $this;
    }
}
