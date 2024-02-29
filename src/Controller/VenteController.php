<?php

namespace App\Controller;

use App\Entity\Vente;
use App\Repository\VenteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class VenteController extends AbstractController
{
    /**
     * @Route("/ventes", name="ventes_index", methods={"GET"})
     */
    public function index(VenteRepository $venteRepository, SerializerInterface $serializer): Response
    {
        // Récupérer toutes les ventes
        $ventes = $venteRepository->findAll();

        // Convertir les ventes en JSON
        $jsonVentes = $serializer->serialize($ventes, 'json');

        // Répondre avec les données JSON
        return new JsonResponse($jsonVentes, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/ventes", name="ventes_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Créer une nouvelle vente
        $vente = new Vente();
        $vente->setProduit($data['produit']);
        $vente->setQuantiteVendue($data['quantite_vendue']);
        $vente->setMontantTotal($data['montant_total']);

        // Sauvegarder la nouvelle vente
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($vente);
        $entityManager->flush();

        // Répondre avec un message de succès
        return new JsonResponse(['message' => 'Vente créée avec succès!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/ventes/{id}", name="ventes_show", methods={"GET"})
     */
    public function show(Vente $vente, SerializerInterface $serializer): Response
    {
        // Convertir la vente en JSON
        $jsonVente = $serializer->serialize($vente, 'json');

        // Répondre avec les données JSON de la vente
        return new JsonResponse($jsonVente, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/ventes/{id}", name="ventes_edit", methods={"PUT"})
     */
    public function edit(Request $request, Vente $vente): Response
    {
        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Mettre à jour la vente
        $vente->setProduit($data['produit']);
        $vente->setQuantiteVendue($data['quantite_vendue']);
        $vente->setMontantTotal($data['montant_total']);

        // Sauvegarder les modifications
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        // Répondre avec un message de succès
        return new JsonResponse(['message' => 'Vente mise à jour avec succès!'], Response::HTTP_OK);
    }

    /**
     * @Route("/ventes/{id}", name="ventes_delete", methods={"DELETE"})
     */
    public function delete(Vente $vente): Response
    {
        // Supprimer la vente
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($vente);
        $entityManager->flush();

        // Répondre avec un message de succès
        return new JsonResponse(['message' => 'Vente supprimée avec succès!'], Response::HTTP_OK);
    }
}
