<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $montant = null;
    
    
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_paiement = null;
    

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $methode_paiement = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    private ?Produit $produit = null;

    #[ORM\OneToMany(targetEntity: PaiementProduit::class, mappedBy: 'paiement', cascade: ["persist", "remove"])]
    private Collection $paiementProduits;
    

    
    public function __construct()
    {
        $this->paiementProduits = new ArrayCollection();
        $this->produits = new ArrayCollection(); 
    }
    
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(\DateTimeInterface $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getMethodePaiement(): ?string
    {
        return $this->methode_paiement;
    }
    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        $this->produits->removeElement($produit);

        return $this;
    }


    public function setMethodePaiement(?string $methode_paiement): static
    {
        $this->methode_paiement = $methode_paiement;

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

    /**
     * @return Collection<int, PaiementProduit>
     */
    public function getPaiementProduits(): Collection
    {
        return $this->paiementProduits;
    }

    public function addPaiementProduit(PaiementProduit $paiementProduit): static
    {
        if (!$this->paiementProduits->contains($paiementProduit)) {
            $this->paiementProduits->add($paiementProduit);
            $paiementProduit->setPaiement($this);
        }

        return $this;
    }

    public function delete(Paiement $paiement): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($paiement);
        $entityManager->flush();
    
        return new Response('Le paiement a été supprimé ', Response::HTTP_NO_CONTENT);
    }
}
