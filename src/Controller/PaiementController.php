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
    
    
    $data = [];

    
    foreach ($paiements as $paiement) {
        
        $paiementData = [
            'id' => $paiement->getId(),
            'montant' => $paiement->getMontant(),
            'date_paiement'=> $paiement->getDatePaiement(),
            'methode_paiement' => $paiement->getMethodePaiement(),
            
        ];

        
        $data[] = $paiementData;
    }

    
    return $this->json($data);
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
    
    
    if ($data === null || !isset($data['montant']) || !isset($data['produit_id'])) {
        return $this->json(['error' => 'Invalid or incomplete data provided'], Response::HTTP_BAD_REQUEST);
    }

    
    $paiement = new Paiement();

    
    $paiement->setMontant($data['montant']);

    
    if (isset($data['date_paiement'])) {
        $datePaiement = new \DateTime($data['date_paiement']);
    } else {
        $datePaiement = new \DateTime(); 
    }
    $paiement->setDatePaiement($datePaiement);

    $paiement->setMethodePaiement($data['moyen_paiement']);

    
    foreach ($data['produit_id'] as $produitId) {
        
        $produit = $this->entityManager->getRepository(Produit::class)->find($produitId);
        
        if ($produit === null) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
    
        
        if ($produit->getQuantiteEnStock() < 1) {
            return $this->json(['error' => 'Product out of stock'], Response::HTTP_BAD_REQUEST);
        }
    
        
        $quantiteVendue = 1; 
        $nouvelleQuantiteEnStock = $produit->getQuantiteEnStock() - $quantiteVendue;
        $produit->setQuantiteEnStock($nouvelleQuantiteEnStock);

        
        $this->entityManager->persist($produit);
    
        
        $paiement->addProduit($produit);
    
        if (isset($data['date_paiement'])) {
            $datedate = new \DateTime($data['date_paiement']);
        } else {
            $datedate = new \DateTime(); 
        }
        
        $paiementProduit = new PaiementProduit();
        $paiementProduit->setPaiement($paiement);
        $paiementProduit->setProduit($produit);
        $paiementProduit->setDatedate($datedate);
        
        $this->entityManager->persist($paiementProduit);
    }
    
    
    $this->entityManager->persist($paiement);
    $this->entityManager->flush();
    
    
    return $this->json('Payment successfully processed');
}




    /**
     * @Route("/paiements/{id}", name="paiement_update", methods={"PUT"})
     */
    public function update(Request $request, Paiement $paiement): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $paiement->setMontant($data['montant']);
        

        $this->entityManager->flush();

        return $this->json($paiement);
    }


    /**
 * @Route("/paiements/chiffre_affaire", name="paiement_chiffre_affaire", methods={"POST"})
 */
public function chiffreAffaireIntervalle(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        
        if (!isset($data['date_debut']) || !isset($data['date_fin'])) {
            return $this->json(['error' => 'Missing start or end date'], Response::HTTP_BAD_REQUEST);
        }

        
        $dateDebut = new \DateTime($data['date_debut']);
        $dateFin = new \DateTime($data['date_fin']);

        
        $paiements = $this->paiementRepository->findByDateInterval($dateDebut, $dateFin);

        
        $chiffreAffaireTotal = 0;
        foreach ($paiements as $paiement) {
            $chiffreAffaireTotal += $paiement->getMontant();
        }

        return $this->json(['chiffre_affaire' => $chiffreAffaireTotal]);
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

/**
     * @Route("/last", name="paiement_last", methods={"GET"})
     */
    public function getLastPayment(Request $request): JsonResponse
    {
        
    $lastPayment = $this->entityManager->getRepository(Paiement::class)
    ->findOneBy([], ['datePaiement' => 'DESC']);

if (!$lastPayment) {
    return $this->json(['error' => 'No payments found'], Response::HTTP_NOT_FOUND);
}


$paiementProduits = $this->entityManager->getRepository(PaiementProduit::class)
    ->findBy(['paiement' => $lastPayment]);


$productDetails = [];
$totalAmount = 0;


foreach ($paiementProduits as $paiementProduit) {
    $produit = $paiementProduit->getProduit();
    $productDetails[] = [
        'nom' => $produit->getNom(),
        'prix_unitaire' => $produit->getPrixUnitaire(),
    ];
    $totalAmount += $produit->getPrixUnitaire();
}


$response = [
    'paiement' => [
        'id' => $lastPayment->getId(),
        'montant' => $lastPayment->getMontant(),
        'date_paiement' => $lastPayment->getDatePaiement()->format('Y-m-d H:i:s'),
        'methode_paiement' => $lastPayment->getMethodePaiement(),
    ],
    'produits' => $productDetails,
    'montant_total' => $totalAmount,
];

return $this->json($response);
    }

/**
 * @Route("/api/print-ticket", name="print_ticket", methods={"GET", "POST"})
 */
    public function printTicket(Request $request): JsonResponse
    {
        
        $lastPayment = $this->entityManager->getRepository(Paiement::class)
            ->findOneBy([], ['date_paiement' => 'DESC']);
    
        if (!$lastPayment) {
            return $this->json(['error' => 'No payments found'], Response::HTTP_NOT_FOUND);
        }
    
        
        $paiementProduits = $this->entityManager->getRepository(PaiementProduit::class)
            ->findBy(['paiement' => $lastPayment]);
    
        
        $productDetails = [];
        $totalAmount = 0;
    
        
        foreach ($paiementProduits as $paiementProduit) {
            $produit = $paiementProduit->getProduit();
            
            $productEntity = $this->entityManager->getRepository(Produit::class)->find($produit->getId());
            if ($productEntity) {
                $productDetails[] = [
                    'nom' => $productEntity->getNom(),
                    'prix_unitaire' => $productEntity->getPrixUnitaire(),
                ];
                $totalAmount += $productEntity->getPrixUnitaire();
            }
        }
    
        
        $response = [
            'paiement' => [
                'id' => $lastPayment->getId(),
                'montant' => $lastPayment->getMontant(),
                'date_paiement' => $lastPayment->getDatePaiement()->format('Y-m-d H:i:s'),
                'methode_paiement' => $lastPayment->getMethodePaiement(),
            ],
            'produits' => $productDetails,
            'montant_total' => $totalAmount,
        ];
    
        return $this->json($response);
    }
    

 
}
