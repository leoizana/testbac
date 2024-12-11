<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Security\AppAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $authenticator,
        #[Autowire(service: 'App\Security\LoginFormAuthenticator')]
        AuthenticatorInterface $formAuthenticator,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Créer une nouvelle instance de l'utilisateur
        $user = new User();
        
        // Créer le formulaire d'inscription
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Encoder le mot de passe
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            ));

            // Définir le rôle 'ROLE_USER' pour l'utilisateur
            $user->setRoles(['ROLE_USER']);  // Ajouter le rôle 'ROLE_USER'

            // Persister l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Authentifier et rediriger
            return $authenticator->authenticateUser(
                $user,
                $formAuthenticator,
                $request
            );
        }

        // Rendre le formulaire d'inscription
        return $this->render('register/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
