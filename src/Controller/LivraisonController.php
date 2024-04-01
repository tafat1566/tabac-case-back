<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Entity\Livraison;
use App\Entity\Produit;
use App\Repository\LivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer; // Ajoutez cette ligne

class LivraisonController extends AbstractController
{
    /**
     * @Route("/livraisons", name="livraisons_list", methods={"GET"})
     */
    public function index(LivraisonRepository $livraisonRepository): JsonResponse
    {
        $livraisons = $livraisonRepository->findAll();

        $livraisonsData = [];
        foreach ($livraisons as $livraison) {
            $livraisonsData[] = [
                'id' => $livraison->getId(),
                'date_livraison' => $livraison->getDateLivraison()->format('Y-m-d H:i:s'),
                'quantite' => $livraison->getQuantite(),
                'fournisseur_id' => $livraison->getFournisseur() ? $livraison->getFournisseur()->getId() : null,
                'produit_id' => $livraison->getProduit() ? $livraison->getProduit()->getId() : null,
                'montant_livraison' => $livraison->getMontantLivraison(),
                'est_reglee' => $livraison->isEstReglee(), // Correction ici
            ];
        }

        return new JsonResponse($livraisonsData);
    }

 



    /**
 * @Route("/livraisons/{id}", name="livraison_show", methods={"GET"})
 */
public function show(Livraison $livraison): JsonResponse
{
    $livraisonData = [
        'id' => $livraison->getId(),
        'date_livraison' => $livraison->getDateLivraison()->format('Y-m-d H:i:s'),
        'quantite' => $livraison->getQuantite(),
        'fournisseur_id' => $livraison->getFournisseur() ? $livraison->getFournisseur()->getId() : null,
        'produit_id' => $livraison->getProduit() ? $livraison->getProduit()->getId() : null,
        'montant_livraison' => $livraison->getMontantLivraison(),
        'est_reglee' => $livraison->isEstReglee(),
    ];

    return new JsonResponse($livraisonData);
}

    /**
     * @Route("/livraisons", name="livraison_create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Récupérer les données du fournisseur et du produit
        $fournisseur = $entityManager->getRepository(Fournisseur::class)->find($data['fournisseur']);
        $produit = $entityManager->getRepository(Produit::class)->find($data['produit']);
        
        // Vérifier si le produit existe
        if (!$produit) {
            return $this->json('Produit introuvable', 404);
        }
    
        // Récupérer la quantité en stock du produit
        $quantiteEnStock = $produit->getQuantiteEnStock();
    
        // Ajouter la quantité de la livraison à la quantité en stock du produit
        $produit->setQuantiteEnStock($quantiteEnStock + $data['quantite']);
    
        // Créer une nouvelle instance de Livraison
        $livraison = new Livraison();
        $livraison->setDateLivraison(new \DateTime($data['dateLivraison']));
        $livraison->setFournisseur($fournisseur); // Assurez-vous que le fournisseur est correctement défini
        $livraison->setQuantite($data['quantite']);
        $livraison->setProduit($produit);
        $livraison->setMontantLivraison($data['montantLivraison']);
        $livraison->setEstReglee($data['estReglee']);
        
        
        $entityManager->persist($livraison);
        $entityManager->flush();
        
        return $this->json('La livraison a été bien enregistrée');
    }
    
    


    /**
     * @Route("/livraisons/{id}", name="livraison_update", methods={"PUT"})
     */
    public function update(Request $request, Livraison $livraison, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        
        if (isset($data['dateLivraison'])) {
            $livraison->setDateLivraison(new \DateTime($data['dateLivraison']));
        }
        if (isset($data['fournisseur'])) {
            $fournisseur = $entityManager->getRepository(Fournisseur::class)->find($data['fournisseur']);
            $livraison->setFournisseur($fournisseur);
        }
        if (isset($data['quantite'])) {
            $ancienneQuantite = $livraison->getQuantite();
            $nouvelleQuantite = $data['quantite'];
            $differenceQuantite = $nouvelleQuantite - $ancienneQuantite;
    
           
            $produit = $livraison->getProduit();
            
            $quantiteEnStock = $produit->getQuantiteEnStock() + $differenceQuantite;
            $produit->setQuantiteEnStock($quantiteEnStock);
        }
        if (isset($data['produit'])) {
            $produit = $entityManager->getRepository(Produit::class)->find($data['produit']);
            $livraison->setProduit($produit);
        }
        if (isset($data['montantLivraison'])) {
            $livraison->setMontantLivraison($data['montantLivraison']);
        }
        if (isset($data['estReglee'])) {
            $livraison->setEstReglee($data['estReglee']);
        }
    
        $entityManager->flush();
    
        
        return $this->json([
            'id' => $livraison->getId(),
            'dateLivraison' => $livraison->getDateLivraison()->format('Y-m-d H:i:s'),
            'fournisseur' => $livraison->getFournisseur()->getId(),
            'quantite' => $livraison->getQuantite(),
            'produit' => $livraison->getProduit()->getId(),
            'montantLivraison' => $livraison->getMontantLivraison(),
            'estReglee' => $livraison->isEstReglee(),
    
        ]);
    }
    
    

    /**
     * @Route("/livraisons/{id}", name="livraison_delete", methods={"DELETE"})
     */
    public function delete(Livraison $livraison, EntityManagerInterface $entityManager): JsonResponse
{
    
    $quantiteLivraison = $livraison->getQuantite();
    // Récupérer le produit associé à la livraison
    $produit = $livraison->getProduit();
   
    $quantiteEnStock = $produit->getQuantiteEnStock() - $quantiteLivraison;
    $produit->setQuantiteEnStock($quantiteEnStock);

    
    $entityManager->remove($livraison);
    $entityManager->flush();

    return $this->json(['message' => 'Livraison deleted']);
}

}