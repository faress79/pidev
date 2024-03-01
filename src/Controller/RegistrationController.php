<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Doctrine\Persistence\ManagerRegistry;






use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/user')]

class RegistrationController extends AbstractController
{
    

   
    #[Route('/login')]
   
    #[Route('/back', name: 'back')]
    public function back(UserRepository $userRepository): Response
    {
        // Assuming you want to retrieve all users
        $users = $userRepository->findAll();
    
        return $this->render('index.html.twig', [
            'b' => $users, // Pass the users data to the template
        ]);
    }
/** 
 * @route("/removeuser/{id}",name="supp_user")
*/
public function supprimer(User $user): Response
{
    $entityManager = $this->getDoctrine()->getManager(); // Correct way to get Doctrine

    $entityManager->remove($user); // Remove the user
    $entityManager->flush(); // Flush changes to the database

    return $this->redirectToRoute('back');
}
    
    /* #[Route('/login')]
    public function login(AssuranceRepository $assuranceRepository): Response
    {
        return $this->render('assurance/login.html.twig');
    }*/
    #[Route('/front', name: 'front')]
    public function front(UserRepository $userRepository): Response
    {
        return $this->render('front.html.twig');
    }
    #[Route('/login/add', name: 'adduser')]

    
    public function adduser(ManagerRegistry $doctrine, Request $request): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    
    // Handle the form submission
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Set roles here after the form submission
        $user->setRoles(['ROLE_USER']);  // Or any other roles
        
        // Persist the user entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Redirect after successful user creation
        return $this->redirectToRoute('front');
    }

    return $this->render('assurance/login.html.twig', [
        'form' => $form->createView(),
    ]);
}
    
   /** 
 * @route("/edituser/{id}",name="edit_user")
*/
public function edituser(ManagerRegistry $doctrine, Request $request, $id): Response
{
    $entityManager = $doctrine->getManager();
    $user = $entityManager->getRepository(User::class)->find($id);

    if (!$user) {
        throw $this->createNotFoundException('User with id ' . $id . ' not found');
    }

    $form = $this->createForm(UserType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        // Redirect to the named route 'back'
        return $this->redirectToRoute('back');
    }

    return $this->render('edituser.html.twig', [
        'form' => $form->createView(),
    ]);
}
  /**
     * @Route("/search", name="search")
     */
    public function search(Request $request): Response
    {
        $form = $this->createForm(Usertype::class);
        $form->handleRequest($request);

        $username = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $searchQuery = $form->get('searchQuery')->getData();

            // Perform search logic, e.g., query the database
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $searchQuery]);

            if ($user) {
                $username = $user->getUsername();
            }
        }

        // Render the template and pass data to it
        return $this->render('index.html.twig', [
            'form' => $form->createView(),
            'username' => $username,
        ]);
    }

     
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


}
