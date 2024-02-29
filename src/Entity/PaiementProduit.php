<?php

namespace App\Entity;

use App\Repository\PaiementProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementProduitRepository::class)]
class PaiementProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'paiementProduits')]
    private ?Paiement $paiement = null;

    #[ORM\ManyToOne(inversedBy: 'paiementProduits')]
    private ?Produit $produit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): static
    {
        $this->paiement = $paiement;

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
}
