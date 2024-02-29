<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FournisseurController extends AbstractController
{
    private $entityManager;
    private $fournisseurRepository;

    public function __construct(EntityManagerInterface $entityManager, FournisseurRepository $fournisseurRepository)
    {
        $this->entityManager = $entityManager;
        $this->fournisseurRepository = $fournisseurRepository;
    }

    /**
     * @Route("/fournisseurs", name="fournisseurs_index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $fournisseurs = $this->fournisseurRepository->findAll();

        $data = [];
        foreach ($fournisseurs as $fournisseur) {
            $data[] = [
                'id' => $fournisseur->getId(),
                'nom' => $fournisseur->getNom(),
                'adresse' => $fournisseur->getAdresse(),
                'email' => $fournisseur->getEmail(),
                'telephone' => $fournisseur->getTelephone(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/fournisseurs", name="fournisseurs_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $fournisseur = new Fournisseur();
        $fournisseur->setNom($data['nom'] ?? null);
        $fournisseur->setAdresse($data['adresse'] ?? null);
        $fournisseur->setEmail($data['email'] ?? null);
        $fournisseur->setTelephone($data['telephone'] ?? null);

        $this->entityManager->persist($fournisseur);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Fournisseur créé avec succès'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/fournisseurs/{id}", name="fournisseurs_show", methods={"GET"})
     */
    public function show(Fournisseur $fournisseur): JsonResponse
    {
        $data = [
            'id' => $fournisseur->getId(),
            'nom' => $fournisseur->getNom(),
            'adresse' => $fournisseur->getAdresse(),
            'email' => $fournisseur->getEmail(),
            'telephone' => $fournisseur->getTelephone(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/fournisseurs/{id}", name="fournisseurs_update", methods={"PUT"})
     */
    public function update(Request $request, Fournisseur $fournisseur): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $fournisseur->setNom($data['nom'] ?? $fournisseur->getNom());
        $fournisseur->setAdresse($data['adresse'] ?? $fournisseur->getAdresse());
        $fournisseur->setEmail($data['email'] ?? $fournisseur->getEmail());
        $fournisseur->setTelephone($data['telephone'] ?? $fournisseur->getTelephone());

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Fournisseur mis à jour avec succès'], Response::HTTP_OK);
    }

    /**
     * @Route("/fournisseurs/{id}", name="fournisseurs_delete", methods={"DELETE"})
     */
    public function delete(Fournisseur $fournisseur): JsonResponse
    {
        $this->entityManager->remove($fournisseur);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Fournisseur supprimé avec succès'], Response::HTTP_OK);
    }
}
