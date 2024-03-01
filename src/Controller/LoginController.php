<?php

// src/Controller/LoginController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\LoginType;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
{
    // If the user is already logged in, redirect them to the homepage
    if ($this->getUser()) {
        return $this->redirectToRoute('front');
    }

    // Get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // Get the last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    // Create the login form
    $loginForm = $this->createForm(LoginFormType::class);

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error'         => $error,
        'loginForm'     => $loginForm->createView(), // Corrected variable name
    ]);
}

    // You can add more methods here, such as logout functionality, registration, etc.
}
