<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Fournisseur;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\FournisseurRepository;


class ProduitController extends AbstractController
{
    private $entityManager;
    private $produitRepository;
    private $fournisseurRepository;
    // private $produitRepository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager, 
        ProduitRepository $produitRepository,
        FournisseurRepository $fournisseurRepository,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->produitRepository = $produitRepository;
        $this->fournisseurRepository = $fournisseurRepository;
        $this->serializer = $serializer;
    }
    
    #[Route('/produits', name: 'produit_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $produits = $this->produitRepository->findAll();
        $data = [];

        foreach ($produits as $produit) {
            $categorieId = null;
            $categorie = $produit->getCategorie();
            if ($categorie !== null) {
                $categorieId = $categorie->getId();
            }
        
            $data[] = [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'description' => $produit->getDescription(),
                'quantite_en_stock' => $produit->getQuantiteEnStock(),
                'prix_unitaire' => $produit->getPrixUnitaire(),
                'categorie_id' => $categorieId, // Utilisez l'identifiant de la catégorie ou null s'il n'y a pas de catégorie associée
            ];
        }
        // Exclure les relations problématiques lors de la sérialisation
        $jsonContent = $this->serializer->serialize($data, 'json', [
            'ignored_attributes' => ['relationA', 'relationB'], // Remplacez 'relationA' et 'relationB' par les noms de vos relations problématiques
        ]);

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }
    

    #[Route('/produits', name: 'produit_create', methods: ['POST'])]
    

    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Vérifier si les données ont été correctement décodées
        if ($data === null || !isset($data['nom']) || !isset($data['prix_unitaire'])) {
            return $this->json(['error' => 'Invalid or incomplete data provided'], Response::HTTP_BAD_REQUEST);
        }
        
        // Continuer le traitement si les données sont correctes
        $produit = new Produit();
        $produit->setNom($data['nom']);
        $produit->setPrixUnitaire($data['prix_unitaire']);
        $produit->setQuantiteEnStock($data['quantite_en_stock'] ?? null);
    
        // Gérez les relations avec d'autres entités, si nécessaire (par exemple, fournisseur)
// Gérez les relations avec d'autres entités, si nécessaire (par exemple, fournisseur)
    if (isset($data['fournisseur_id'])) {
    // Récupérez d'abord l'entité Fournisseur correspondant à l'ID fourni
    $fournisseur = $this->entityManager->getRepository(Fournisseur::class)->find($data['fournisseur_id']);
    if ($fournisseur === null) {
        return $this->json(['error' => 'Supplier not found'], Response::HTTP_NOT_FOUND);
    }

    // Assurez-vous que $fournisseur est bien une instance de Fournisseur
    if (!$fournisseur instanceof Fournisseur) {
        return $this->json(['error' => 'Invalid supplier provided'], Response::HTTP_BAD_REQUEST);
    }

    // Assignez l'entité Fournisseur au Produit
    $produit->setFournisseur($fournisseur);
}
if (isset($data['categorie_id'])) {
    // Récupérez l'entité Categorie correspondant à l'ID fourni
    $categorie = $this->entityManager->getRepository(Categorie::class)->find($data['categorie_id']);
    if ($categorie === null) {
        return $this->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
    }

    // Assurez-vous que $categorie est bien une instance de Categorie
    if (!$categorie instanceof Categorie) {
        return $this->json(['error' => 'Invalid category provided'], Response::HTTP_BAD_REQUEST);
    }

    // Assignez l'entité Categorie au Produit
    $produit->setCategorie($categorie);
}
        // Persistez l'entité dans la base de données
        $this->entityManager->persist($produit);
        $this->entityManager->flush();
    
        // Exclure les relations problématiques lors de la sérialisation
        $jsonContent = $serializer->serialize($produit, 'json', [
            'ignored_attributes' => ['relationA', 'relationB'], // Remplacez 'relationA' et 'relationB' par les noms de vos relations problématiques
        ]);
    
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }
    
    #[Route('/produits/{id}', name: 'produit_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $produit = $this->produitRepository->find($id);

        if (!$produit) {
            return $this->json(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'prix_unitaire' => $produit->getPrixUnitaire(), // Assurez-vous d'avoir une méthode getPrixUnitaire() dans votre entité Produit
            // Vous pouvez ajouter d'autres propriétés du produit si nécessaire
            'categorie_id' => $produit->getCategorie()->getId(),

            
        ];

        return $this->json($data);
    }

    #[Route('/produits/{id}', name: 'produit_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Récupération du produit à mettre à jour
        $produit = $this->produitRepository->find($id);
    
        if (!$produit) {
            return $this->json(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        // Mise à jour des champs du produit
        $produit->setNom($data['nom']);
        $produit->setDescription($data['description']);
        $produit->setPrixUnitaire($data['prix_unitaire']);
        $produit->setQuantiteEnStock($data['quantite_en_stock']);
        
        // Vous devez utiliser la méthode setFournisseur() si la relation est ManyToOne
        $fournisseur = $this->fournisseurRepository->find($data['fournisseur_id']);
        $produit->setFournisseur($fournisseur);
        if (isset($data['categorie_id'])) {
            // Récupérez l'entité Categorie correspondant à l'ID fourni
            $categorie = $this->entityManager->getRepository(Categorie::class)->find($data['categorie_id']);
            if ($categorie === null) {
                return $this->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
            }

            // Assurez-vous que $categorie est bien une instance de Categorie
            if (!$categorie instanceof Categorie) {
                return $this->json(['error' => 'Invalid category provided'], Response::HTTP_BAD_REQUEST);
            }

            // Assignez la nouvelle catégorie au Produit
            $produit->setCategorie($categorie);
        }
        // Enregistrement des changements dans la base de données
        $this->entityManager->flush();
        
        // Retourner le produit mis à jour
        return $this->json($produit);
    }
    
    
    #[Route('/produits/{id}', name: 'produit_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $produit = $this->produitRepository->find($id);

        if (!$produit) {
            return $this->json(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($produit);
        $this->entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
