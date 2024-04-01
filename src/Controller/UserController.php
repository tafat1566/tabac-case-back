<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
 * @Route("/api/users", name="api_users_get", methods={"GET"})
 */
public function getAllUsers(): Response
{
    // Récupérer tous les utilisateurs depuis la base de données
    $userRepository = $this->entityManager->getRepository(User::class);
    $users = $userRepository->findAll();

    // Transformer les objets User en tableau associatif
    $usersArray = [];
    foreach ($users as $user) {
        $usersArray[] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ];
    }

    // Renvoyer la liste des utilisateurs sous forme de réponse JSON
    return new JsonResponse($usersArray, Response::HTTP_OK);
}


    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        // Récupérer les données de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données requises sont présentes
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Veuillez fournir un email et un mot de passe'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer l'utilisateur à partir de l'email
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $data['email']]);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si le mot de passe est correct
        if (!password_verify($data['password'], $user->getPassword())) {
            return new JsonResponse(['message' => 'Mot de passe incorrect'], Response::HTTP_UNAUTHORIZED);
        }

        // Gérer l'authentification de l'utilisateur ici
        // Votre logique d'authentification va ici

        // Vous pouvez personnaliser la réponse selon vos besoins
        return new JsonResponse(['message' => 'Authentification réussie'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        // Créer un nouvel utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']); // Ajoutez cette ligne
        $user->setLastName($data['lastName']);   // Ajoutez cette ligne
        
        // Hacher le mot de passe
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        
        // Sauvegarder l'utilisateur dans la base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return new JsonResponse(['message' => 'Utilisateur créé avec succès'], Response::HTTP_CREATED);
    }

/**
 * @Route("/api/user/{id}", name="api_user_get", methods={"GET"})
 */
public function findUserById(int $id): ?UserInterface
{
    // Récupérer l'utilisateur à partir de l'ID
    $userRepository = $this->entityManager->getRepository(User::class);
    $user = $userRepository->find($id);

    return $user;
}
/**
 * @Route("/api/user/{id}", name="api_user_update", methods={"PUT"})
 */
public function updateUser(Request $request, int $id): Response
{
    // Récupérer l'utilisateur à partir de l'ID
    $userRepository = $this->entityManager->getRepository(User::class);
    $user = $userRepository->find($id);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
    }

    // Récupérer les données de la requête
    $data = json_decode($request->getContent(), true);

    // Mettre à jour les propriétés de l'utilisateur
    $user->setEmail($data['email']);
    $user->setFirstName($data['firstName']);
    $user->setLastName($data['lastName']);

    // Sauvegarder les modifications dans la base de données
    $this->entityManager->flush();

    // Renvoyer une réponse indiquant que la mise à jour a réussi
    return new JsonResponse(['message' => 'Utilisateur mis à jour avec succès'], Response::HTTP_OK);
}

/**
 * @Route("/api/user/{id}", name="api_user_delete", methods={"DELETE"})
 */
public function deleteUser(int $id): Response
{
    
    $userRepository = $this->entityManager->getRepository(User::class);
    $user = $userRepository->find($id);

   
    if (!$user) {
        return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
    }

    
    $this->entityManager->remove($user);
    $this->entityManager->flush();

    
    return new JsonResponse(['message' => 'Utilisateur supprimé avec succès'], Response::HTTP_OK);
}

    
}
