<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\editUserType;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Twilio\Rest\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailTransport;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Doctrine\Persistence\ManagerRegistry;









use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Reservation;

use  Flasher\Prime\FlasherInterface;
use App\Form\ReservationFormType;
use App\Repository\VoitureRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\Routing\RouterInterface;
use Knp\Component\Pager\PaginatorInterface;


use Symfony\Component\HttpFoundation\JsonResponse;






use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

#[Route('/user')]
class RegistrationController extends AbstractController
{
    #[Route('/signup', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $sid    = "ACbb7bdd4c3e67b3ca0f7c94f9757e70b9";
        $token  = "000b58d24296253e819dc1f8f4929cd6";
        $twilio = new Client($sid, $token);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            /*
    $message = $twilio->messages
      ->create("+21627663060", // to
        array(
          "from" => "+12677298034",
          "body" => "Your Message"
        )
      );*/

        
        // Persist the user entity

            $entityManager->persist($user);
            $entityManager->flush();
            $email = (new Email())
    ->from('symfonycopte822@gmail.com')
    ->to($form->get('email')->getData())
    ->subject('new account creation  Confirmation')
    ->text('Dear user,

Thank you for choosing to rent  from Trippie. We are pleased to confirm your reservation for the to .

As a reminder, please bring a valid driver\'s license and a credit card in your name when you come to pick up the car. If you have any additional questions or special requests, please do not hesitate to contact us.

We look forward to serving you and hope you have a safe and enjoyable rental experience with us.

Best regards,
Trippie');

$transport=new GmailSmtpTransport('symfonycopte822@gmail.com', 'cdwgdrevbdoupxhn');
$mailer=new Mailer($transport);
$mailer->send($email);
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
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
#[Route('/back', name: 'back')]
public function back(UserRepository $userRepository): Response
{
    // Assuming you want to retrieve all users
    $users = $userRepository->findAll();

    return $this->render('user/index.html.twig', [
        'b' => $users, // Pass the users data to the template
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

    $form = $this->createForm(editUserType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        // Redirect to the named route 'back'
        return $this->redirectToRoute('back');
    }

    return $this->render('user/edituser.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/dashboard/stat', name: 'stat', methods: ['POST','GET'])]
public function VoitureStatistics(UserRepository $repo): Response {
    // Count the total number of users with 'ROLE_ADMIN' and 'ROLE_USER'
    $totalAdminCount = $repo->countByLibelle(['ROLE_ADMIN']);
    $totalUserCount = $repo->countByLibelle(['ROLE_USER']);

    // Calculate the total count of users
    $total = $totalAdminCount + $totalUserCount;

    // Calculate the percentages, ensuring to handle division by zero
    $adminPercentage = $total > 0 ? round(($totalAdminCount / $total) * 100) : 0;
    $userPercentage = $total > 0 ? round(($totalUserCount / $total) * 100) : 0;

    // Render the template with the calculated percentages
    return $this->render('user/stat.html.twig', [
        'adminPercentage' => $adminPercentage,
        'userPercentage' => $userPercentage,
    ]);
}
}
