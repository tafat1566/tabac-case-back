<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategorieController extends AbstractController
{
    private $entityManager;
    private $categorieRepository;

    public function __construct(
        EntityManagerInterface $entityManager, 
        CategorieRepository $categorieRepository
    ) {
        $this->entityManager = $entityManager;
        $this->categorieRepository = $categorieRepository;
    }
    
    #[Route('/categories', name: 'categorie_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $categories = $this->categorieRepository->findAll();
        $data = [];
    
        foreach ($categories as $categorie) {
            $data[] = [
                'id' => $categorie->getId(),
                'nom' => $categorie->getNom(),
                'description' => $categorie->getDescription(),
                // Vous pouvez ajouter d'autres propriétés de la catégorie si nécessaire
            ];
        }
    
        return $this->json($data);
    }
    

    #[Route('/categories', name: 'categorie_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Vérifier si les données ont été correctement décodées
        if ($data === null || !isset($data['nom'])) {
            return $this->json(['error' => 'Invalid or incomplete data provided'], Response::HTTP_BAD_REQUEST);
        }
        
        // Continuer le traitement si les données sont correctes
        $categorie = new Categorie();
        $categorie->setNom($data['nom']);
        $categorie->setDescription($data['description'] ?? null);
    
        // Persistez l'entité dans la base de données
        $this->entityManager->persist($categorie);
        $this->entityManager->flush();
    
        // Exclure les relations problématiques lors de la sérialisation
        $jsonContent = $serializer->serialize($categorie, 'json', [
            'ignored_attributes' => ['relationA', 'relationB'], // Remplacez 'relationA' et 'relationB' par les noms de vos relations problématiques
        ]);
    
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }
    
    #[Route('/categories/{id}', name: 'categorie_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $categorie = $this->categorieRepository->find($id);

        if (!$categorie) {
            return $this->json(['message' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $categorie->getId(),
            'nom' => $categorie->getNom(),
            'description' => $categorie->getDescription(),
        ];

        return $this->json($data);
    }

    #[Route('/categories/{id}', name: 'categorie_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Récupération de la catégorie à mettre à jour
        $categorie = $this->categorieRepository->find($id);
    
        if (!$categorie) {
            return $this->json(['message' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }
    
        // Mise à jour des champs de la catégorie
        $categorie->setNom($data['nom']);
        $categorie->setDescription($data['description']);
        
        // Enregistrement des changements dans la base de données
        $this->entityManager->flush();
        
        // Retourner la catégorie mise à jour
        return $this->json($categorie);
    }
    
    
    #[Route('/categories/{id}', name: 'categorie_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $categorie = $this->categorieRepository->find($id);

        if (!$categorie) {
            return $this->json(['message' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($categorie);
        $this->entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
