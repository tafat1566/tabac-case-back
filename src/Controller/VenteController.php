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
        
        $ventes = $venteRepository->findAll();

        
        $jsonVentes = $serializer->serialize($ventes, 'json');

        
        return new JsonResponse($jsonVentes, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/ventes", name="ventes_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        
        $data = json_decode($request->getContent(), true);

        
        $vente = new Vente();
        $vente->setProduit($data['produit']);
        $vente->setQuantiteVendue($data['quantite_vendue']);
        $vente->setMontantTotal($data['montant_total']);

        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($vente);
        $entityManager->flush();

        
        return new JsonResponse(['message' => 'Vente créée avec succès!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/ventes/{id}", name="ventes_show", methods={"GET"})
     */
    public function show(Vente $vente, SerializerInterface $serializer): Response
    {
        
        $jsonVente = $serializer->serialize($vente, 'json');

        
        return new JsonResponse($jsonVente, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/ventes/{id}", name="ventes_edit", methods={"PUT"})
     */
    public function edit(Request $request, Vente $vente): Response
    {
        
        $data = json_decode($request->getContent(), true);

        
        $vente->setProduit($data['produit']);
        $vente->setQuantiteVendue($data['quantite_vendue']);
        $vente->setMontantTotal($data['montant_total']);

        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        
        return new JsonResponse(['message' => 'Vente mise à jour avec succès!'], Response::HTTP_OK);
    }

    /**
     * @Route("/ventes/{id}", name="ventes_delete", methods={"DELETE"})
     */
    public function delete(Vente $vente): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($vente);
        $entityManager->flush();

        
        return new JsonResponse(['message' => 'Vente supprimée avec succès!'], Response::HTTP_OK);
    }
}
