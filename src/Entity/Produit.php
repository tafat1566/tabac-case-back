<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $prixUnitaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantiteEnStock = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\OneToMany(targetEntity: Paiement::class, mappedBy: 'produit')]
    private Collection $paiements;

    #[ORM\OneToMany(targetEntity: PaiementProduit::class, mappedBy: 'produit')]
    private Collection $paiementProduits;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'produitt')]
    private Collection $stocks;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
        $this->paiementProduits = new ArrayCollection();
        $this->stocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getQuantiteEnStock(): ?int
    {
        return $this->quantiteEnStock;
    }

    public function setQuantiteEnStock(?int $quantiteEnStock): static
    {
        $this->quantiteEnStock = $quantiteEnStock;

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setProduit($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            
            if ($paiement->getProduit() === $this) {
                $paiement->setProduit(null);
            }
        }

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
            $paiementProduit->setProduit($this);
        }

        return $this;
    }

    public function removePaiementProduit(PaiementProduit $paiementProduit): static
    {
        if ($this->paiementProduits->removeElement($paiementProduit)) {
            
            if ($paiementProduit->getProduit() === $this) {
                $paiementProduit->setProduit(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setProduitt($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            
            if ($stock->getProduitt() === $this) {
                $stock->setProduitt(null);
            }
        }

        return $this;
    }
}
