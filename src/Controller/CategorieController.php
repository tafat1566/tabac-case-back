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
                
            ];
        }
    
        return $this->json($data);
    }
    

    #[Route('/categories', name: 'categorie_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        
        if ($data === null || !isset($data['nom'])) {
            return $this->json(['error' => 'Invalid or incomplete data provided'], Response::HTTP_BAD_REQUEST);
        }
        
        
        $categorie = new Categorie();
        $categorie->setNom($data['nom']);
        $categorie->setDescription($data['description'] ?? null);
        $categorie->getImage($data['image']);
        
        
        $this->entityManager->persist($categorie);
        $this->entityManager->flush();
    
        
        $jsonContent = $serializer->serialize($categorie, 'json', [
            'ignored_attributes' => ['relationA', 'relationB'], 
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
            'image' => $categorie->getImage(),
        ];

        return $this->json($data);
    }

    #[Route('/categories/{id}', name: 'categorie_update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
    
        if (!$categorie) {
            return new JsonResponse(['message' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }
    
        
        if (isset($data['nom'])) {
            $categorie->setNom($data['nom']);
        }
        if (isset($data['description'])) {
            $categorie->setDescription($data['description']);
        }
    
        
        try {
            $entityManager->flush();
            
            $updatedData = [
                'id' => $categorie->getId(),
                'nom' => $categorie->getNom(),
                'description' => $categorie->getDescription(),
            ];
            return new JsonResponse($updatedData, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la mise à jour de la catégorie'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
