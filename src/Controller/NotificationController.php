<?php

// src/Controller/NotificationController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produit;

class NotificationController extends AbstractController
{
    /**
     * @Route("/notifications", name="get_notifications", methods={"GET"})
     */
    public function getNotifications(EntityManagerInterface $entityManager): JsonResponse
    {
        $notifications = $entityManager->getRepository(Notification::class)->findAll();

        return $this->json($notifications);
    }

/**
 * @Route("/notifications", name="create_notification", methods={"POST"})
 */
public function createNotification(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $notification = new Notification();
    $notification->setMessage($data['message']);
    $notification->setType($data['type']);
    $notification->setDateCreation(new \DateTime());
    $notification->setLu(false); // Définit par défaut à false

    // Récupère la liste des produits
    $products = $entityManager->getRepository(Produit::class)->findAll();

    // Parcours la liste des produits
    foreach ($products as $product) {
        // Vérifie si la quantité en stock est inférieure à 10
        if ($product->getQuantiteEnStock() < 10) {
            // Crée une nouvelle notification
            $newNotification = new Notification();
            $newNotification->setMessage('La quantité en stock du produit ' . $product->getNom() . ' est inférieure à 10.');
            $newNotification->setType('warning'); // Peut-être que 'warning' convient pour ce type de notification
            $newNotification->setDateCreation(new \DateTime());
            $newNotification->setLu(false); // Définit par défaut à false

            // Persiste la nouvelle notification dans la base de données
            $entityManager->persist($newNotification);
        }
    }

    // Persiste la notification initiale
    $entityManager->persist($notification);
    $entityManager->flush();

    return $this->json(['message' => 'Notification created'], 201);
}


    /**
     * @Route("/notifications/{id}/mark-as-read", name="mark_notification_as_read", methods={"PUT"})
     */
    public function markNotificationAsRead($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $notification = $entityManager->getRepository(Notification::class)->find($id);

        if (!$notification) {
            return $this->json(['message' => 'Notification not found'], Response::HTTP_NOT_FOUND);
        }

        $notification->setLu(true);
        $entityManager->flush();

        return $this->json(['message' => 'Notification marked as read']);
    }

        /**
     * @Route("/notifications/latest", name="get_latest_notifications", methods={"GET"})
     */
    public function getLatestNotifications(EntityManagerInterface $entityManager): JsonResponse
    {
        $notifications = $entityManager->getRepository(Notification::class)
            ->findBy([], ['dateCreation' => 'DESC'], 20); // Récupère les 20 dernières notifications, triées par date de création décroissante

        // Vous pouvez ajouter d'autres logiques de traitement si nécessaire avant de renvoyer les notifications

        return $this->json($notifications);
    }
    // Ajoutez d'autres actions API pour mettre à jour et supprimer les notifications si nécessaire
}
