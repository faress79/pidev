<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Entity\Contrat;
use App\Form\ContratType;
use App\Form\ContratTypef;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EmailService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Dompdf\Dompdf;
use Dompdf\Options;
#[Route('/contrat')]
class ContratController extends AbstractController
{
    #[Route('/contrats/pdf', name: 'contrats_pdf')]
    public function generatePdf(): Response
    {
        // Fetch all Contrat entities
        $contrats = $this->getDoctrine()->getRepository(Contrat::class)->findAll();

        // Create a Dompdf instance
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf->setOptions($options);

        // Render the Twig template
        $html = $this->renderView('contrat/pdf_all.html.twig', [
            'contrats' => $contrats,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // Set paper size (optional)
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (first render the view, then create the PDF)
        $dompdf->render();

        // Stream the file for download
        $output = $dompdf->output();

        $response = new Response($output);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="contrats.pdf"');

        return $response;
    }
    
    #[Route('/', name: 'app_contrat_index', methods: ['GET'])]
    public function index(ContratRepository $contratRepository): Response
    {
        return $this->render('contrat/index.html.twig', [
            'contrats' => $contratRepository->findAll(),
        ]);
    }
//composer require symfony/mailer
    #[Route('/send-email')]
    public function sendEmail(MailerInterface $mailer,$userName,$avisTitle,$avisViews) {
        $htmlContent = '
        <style>
            /* Style CSS pour le lien */
            .button-link {
                display: inline-block;
                font-size: 14px;
                color: #ffffff;
                text-decoration: none;
                padding: 10px 20px;
                background-color: #0073ff;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }
            .button-link:hover {
                background-color: #0056b3;
            }
            /* Style pour la bordure */
            .container {
                border: 2px solid #0073ff;
                padding: 20px;
                border-radius: 15px;
            }
        </style>
        <h1 style="color: #fff300; background-color: #0073ff; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">
            Félicitations ! Votre avis a reçu plus de 100 vues
        </h1>
        <div class="container" style="font-family: Arial, sans-serif;">
            <p>Cher(e) ' . $userName . ',</p>
            <p>Nous sommes ravis de vous informer que votre avis intitulé "' . $avisTitle . '" a été vu par plus de 100 personnes.</p>
            <p>Cet engouement pour votre avis est remarquable et reflète son importance pour notre communauté.</p>
            <p>Au total, votre avis a atteint ' . $avisViews . ' vues. Merci pour votre précieuse contribution !</p>
            <a href="https://127.0.0.1:8000/avis" class="button-link" target="_blank" style="margin-top: 20px; display: inline-block;">Cliquez ici pour en savoir plus</a>
            <p>Cordialement,</p>
        </div>
        <!-- Image Amazon -->
        <img src="https://img.freepik.com/premium-photo/customer-hand-review-feedback-five-star-rating-service-product-quality-positive-ranking-background-best-evaluation-user-experience-success-business-rate-businessman-satisfaction-5-score_79161-2307.jpg?w=360" alt="Image Amazon" style="max-width: 100%; border: 2px solid #0073ff; border-radius: 15px; margin-top: 20px;">
    ';
    
    
        $email = (new Email())
            ->from('aminfsm2001@gmail.com')
            ->to('nasriamin300@gmail.com')
            ->subject('Votre avis a atteint plus de 100 vues')
            ->html($htmlContent);
    
        try {
            $mailer->send($email);
            return new Response('Email envoyé avec succès!');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/front', name: 'app_contrat_indexf', methods: ['GET'])]
    public function indexf(ContratRepository $contratRepository): Response
    {
        return $this->render('contrat/indexf.html.twig', [
            'contrats' => $contratRepository->findAll(),
        ]);
    }

    
    #[Route('/new', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contrat);
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_indexf', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contrat/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }



    #[Route('/front/new/{agenceId}', name: 'app_contrat_newf', methods: ['GET', 'POST'])]
    public function newf(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, int $agenceId): Response
    {
        // Retrieve the agent based on the provided agenceId
        $agent = $this->getDoctrine()->getRepository(Agence::class)->find($agenceId);

        // Create a new contract associated with the agent
        $contrat = new Contrat();
        $contrat->setAgence($agent); // Set the agent for the contract

        $form = $this->createForm(ContratTypef::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contrat);
            $entityManager->flush();

            // Send email to admin
            $this->sendEmailToAdmin($mailer, $contrat);

            return $this->redirectToRoute('app_contrat_indexf', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contrat/newf.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }



    private function sendEmailToAdmin(MailerInterface $mailer, Contrat $contrat)
{
    $adminEmail = 'nasriamin300@gmail.com'; // Change this to the admin's email

    // Create email content
    $htmlContent = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                /* Add your styles here */
                body {
                    font-family: Arial, sans-serif;
                }
                .container {
                    border: 2px solid #0073ff;
                    padding: 20px;
                    border-radius: 15px;
                }
                .button-link {
                    display: inline-block;
                    font-size: 14px;
                    color: #ffffff;
                    text-decoration: none;
                    padding: 10px 20px;
                    background-color: #0073ff;
                    border-radius: 5px;
                    transition: background-color 0.3s ease;
                }
                .button-link:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>New Contract Added</h2>
                <p>Dear Admin,</p>
                <p>A new contract has been added. Below are the details:</p>
                <ul>
                <li><strong>User:</strong> ' . $contrat->getUtilisateur()->getNom() . '</li>
                    <li><strong>Contract ID:</strong> ' . $contrat->getId() . '</li>
                    <li><strong>Agent:</strong> ' . $contrat->getAgence()->getNom() . '</li>
                    <li><strong>Prix:</strong> ' . $contrat->getPrixContrat() . '</li>
                </ul>
                <p>Please review the details and take any necessary actions.</p>
                <a href="https://example.com" class="button-link" target="_blank">View Contracts</a>
                <p>Best regards,</p>
            </div>
        </body>
        </html>
    ';

    // Create the email
    $email = (new Email())
        ->from('aminfsm2001@gmail.com')
        ->to($adminEmail)
        ->subject('New Contract Added')
        ->html($htmlContent);

    // Send the email
    try {
        $mailer->send($email);
    } catch (\Exception $e) {
      
    }
}
    #[Route('/{id}', name: 'app_contrat_show', methods: ['GET'])]
    public function show(Contrat $contrat): Response
    {
        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
        ]);
    }

    #[Route('/front/{id}', name: 'app_contrat_showf', methods: ['GET'])]
    public function showf(Contrat $contrat): Response
    {
        return $this->render('contrat/showf.html.twig', [
            'contrat' => $contrat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    #[Route('/front/{id}/edit', name: 'app_contrat_editf', methods: ['GET', 'POST'])]
    public function editff(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_indexf', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contrat/editf.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contrat_delete', methods: ['POST'])]
    public function delete(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contrat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('frfr/{id}', name: 'app_contrat_deletef', methods: ['POST'])]
    public function deletef(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contrat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contrat_indexf', [], Response::HTTP_SEE_OTHER);
    }
}
