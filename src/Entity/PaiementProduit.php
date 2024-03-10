<?php

namespace App\Entity;

use App\Repository\PaiementProduitRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datedate = null;

    
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

    public function getDatedate(): ?\DateTimeInterface
    {
        return $this->datedate;
    }

    public function setDatedate(?\DateTimeInterface $datedate): static
    {
        $this->datedate = $datedate;

        return $this;
    }

   

}
