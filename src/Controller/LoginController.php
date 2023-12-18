<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();
         
        
        return $this->render('login/login.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
        
        $this->redirectToRoute('app_home');
        
        
    }

    #[Route('/deconnexion', name: 'logout')]
    public function logout() : RedirectResponse
    {
        // return $this->redirectToRoute('app_home');
    }
}
