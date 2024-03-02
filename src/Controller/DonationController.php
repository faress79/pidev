<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Form\DonationType;
use App\Repository\DonationRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Event;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/donation')]
class DonationController extends AbstractController
{
    #[Route('/', name: 'app_donation_index', methods: ['GET'])]
    public function index(DonationRepository $donationRepository,PaginatorInterface $paginator,Request $request): Response
    {

        $query = $donationRepository->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('donation/index.html.twig', [

            'donations' => $pagination,

        ]);
        /*
        return $this->render('donation/index.html.twig', [
            'donations' => $donationRepository->findAll(),
        ]);
        */
    }

    #[Route('/new/{id}', name: 'app_donation_new', methods: ['GET', 'POST'])]
    public function new(EventRepository $eventRepository,Request $request, EntityManagerInterface $entityManager,MailerInterface $mailer,$id,Security $security): Response
    {
        
      
        $event = $eventRepository->find($id);
        $user = $security->getUser();

        if (!$event) {
            throw $this->createNotFoundException('The event does not exist');
        }
        if (!$user) {
            throw $this->createNotFoundException('You must be logged in to make a donation');
        }
        $donation = new Donation();
        $donation->setEvent($event);
        $donation->setUtilisateur($user); 
        $form = $this->createForm(DonationType::class, $donation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($donation);
            $entityManager->flush();
            $msg='your donation for event: '.$event->getNomEvent().' is confirmed';
            $email = (new Email())
        ->from('gadourkaddachi000@gmail.com')
        ->to($user->getUserIdentifier())
        ->subject('DONATION')
        ->text($msg);

    // Send the email
    $mailer->send($email);

            return $this->redirectToRoute('app_event_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('donation/new.html.twig', [
            'donation' => $donation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_donation_show', methods: ['GET'])]
    public function show(Donation $donation): Response
    {
        return $this->render('donation/show.html.twig', [
            'donation' => $donation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_donation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Donation $donation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DonationType::class, $donation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_donation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('donation/edit.html.twig', [
            'donation' => $donation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_donation_delete', methods: ['POST'])]
    public function delete(Request $request, Donation $donation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$donation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($donation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_donation_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/donation/pdf', name: 'app_donation_pdf', methods: ['GET'])]
public function pdf(DonationRepository $donationRepository): Response
{
    $donations = $donationRepository->findAll();

    // Generate HTML content for the list of donations
    $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);



        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('donation/pdf.html.twig', [
            'donations' => $donations,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $output = $dompdf->output();

        $publicDirectory = $this->getParameter('donation_pdf_directory');

        $pdfFilepath =  $publicDirectory . '/' . uniqid() . '.pdf';
        if (!file_exists($pdfFilepath)) {
            file_put_contents($pdfFilepath, $output);
            return new Response("The PDF file has been succesfully generated !");
        } else {
            return new Response("The PDF file already exist");
        }



        // Output the generated PDF to Browser (force download)

    }
    #[Route('/donation/recu', name: 'app_donation_mail', methods: ['GET'])]
    public function sendEmail(MailerInterface $mailer)
{
    // Create a new Email message
    $email = (new Email())
        ->from('mailjavam@gmail.com')
        ->to('recipient@example.com')
        ->subject('DONATION')
        ->text('your donation is confirmed.');

    // Send the email
    $mailer->send($email);

    // Optionally, handle any exceptions
}

}
