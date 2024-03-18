<?php



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
    $notification->setLu(false); 

    
    $products = $entityManager->getRepository(Produit::class)->findAll();

    
    foreach ($products as $product) {
        
        if ($product->getQuantiteEnStock() < 10) {
            
            $newNotification = new Notification();
            $newNotification->setMessage('La quantité en stock du produit ' . $product->getNom() . ' est inférieure à 10.');
            $newNotification->setType('warning'); 
            $newNotification->setDateCreation(new \DateTime());
            $newNotification->setLu(false); 

            
            $entityManager->persist($newNotification);
        }
    }

    
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
            ->findBy([], ['dateCreation' => 'DESC'], 20); 

        

        return $this->json($notifications);
    }
    
}
