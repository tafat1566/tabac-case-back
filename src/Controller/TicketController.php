<?php
// src/Controller/TicketController.php

namespace App\Controller;


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Paiement;

class TicketController extends AbstractController
{
    /**
     * @Route("/api/print-ticket", name="print_ticket", methods={"GET"})
     */
    public function printTicket(): JsonResponse
    {
        // Retrieve the last payment made
        $lastPayment = $this->getDoctrine()
            ->getRepository(Paiement::class)
            ->findOneBy([], ['date_paiement' => 'DESC']);

        if (!$lastPayment) {
            return new JsonResponse(['error' => 'No payments found'], 404);
        }

        // Generate the ticket content
        $ticketContent = $this->generateTicketContent($lastPayment);

        // Return the ticket content as JSON response
        return new JsonResponse(['ticket' => $ticketContent]);
    }

    /**
     * Generate the ticket content based on the last payment.
     */
    private function generateTicketContent(Paiement $payment): array
    {
        // Example ticket content, replace with your own logic
        $ticketContent = [
            'id' => $payment->getId(),
            'montant' => $payment->getMontant(),
            'date_paiement' => $payment->getDatePaiement()->format('Y-m-d H:i:s'),
            'methode_paiement' => $payment->getMethodePaiement(),
            // Add more information as needed
        ];

        return $ticketContent;
    }
}
