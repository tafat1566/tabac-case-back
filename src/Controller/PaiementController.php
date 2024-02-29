<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Entity\Produit;
use App\Entity\PaiementProduit;
use App\Repository\PaiementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    private $entityManager;
    private $paiementRepository;

    public function __construct(EntityManagerInterface $entityManager, PaiementRepository $paiementRepository)
    {
        $this->entityManager = $entityManager;
        $this->paiementRepository = $paiementRepository;
    }

    /**
     * @Route("/paiements", name="paiement_index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $paiements = $this->paiementRepository->findAll();
        return $this->json($paiements);
    }

    /**
     * @Route("/paiements/{id}", name="paiement_show", methods={"GET"})
     */
    public function show(Paiement $paiement): JsonResponse
    {
        return $this->json($paiement);
    }

/**
 * @Route("/paiements", name="paiement_create", methods={"POST"})
 */
public function create(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    // dd($data);
    // Vérifiez si les données ont été correctement décodées
    if ($data === null || !isset($data['montant']) || !isset($data['produit_id'])) {
        return $this->json(['error' => 'Invalid or incomplete data provided'], Response::HTTP_BAD_REQUEST);
    }

    // Créez une nouvelle instance de Paiement
    $paiement = new Paiement();

    // Définissez les différentes propriétés du paiement en fonction des données reçues
    $paiement->setMontant($data['montant']);

    // Vérifiez si une date de paiement est fournie, sinon utilisez la date actuelle
    if (isset($data['date_paiement'])) {
        $datePaiement = new \DateTime($data['date_paiement']);
    } else {
        $datePaiement = new \DateTime(); // Date et heure actuelles
    }
    $paiement->setDatePaiement($datePaiement);

    $paiement->setMethodePaiement('espece');

    // Gérez les relations avec les produits
    foreach ($data['produit_id'] as $produitId) {
        $produit = $this->entityManager->getRepository(Produit::class)->find($produitId);
        
        if ($produit === null) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        // Utilisez la méthode addProduit() pour ajouter le produit au paiement
        $paiement->addProduit($produit);

        // Créez une nouvelle instance de PaiementProduit
        $paiementProduit = new PaiementProduit();
        $paiementProduit->setPaiement($paiement);
        $paiementProduit->setProduit($produit);

        // Persistez l'entité PaiementProduit dans la base de données
        $this->entityManager->persist($paiementProduit);
    }

    // Persistez l'entité Paiement dans la base de données
    $this->entityManager->persist($paiement);
    $this->entityManager->flush();

    // Retournez la réponse JSON avec les données du paiement nouvellement créé
    var_dump('azul',$produit);
    return $this->json('azul');
}




    /**
     * @Route("/paiements/{id}", name="paiement_update", methods={"PUT"})
     */
    public function update(Request $request, Paiement $paiement): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $paiement->setMontant($data['montant']);
        // Assurez-vous de mettre à jour d'autres champs de la même manière

        $this->entityManager->flush();

        return $this->json($paiement);
    }

    /**
     * @Route("/paiements/{id}", name="paiement_delete", methods={"DELETE"})
     */
    public function delete(Paiement $paiement): Response
    {
        $this->entityManager->remove($paiement);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
