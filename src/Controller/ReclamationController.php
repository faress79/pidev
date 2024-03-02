<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Client;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    public $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(Reclamation $reclam)
    {
        $email = (new Email())
            ->from('marweng.touati@gmail.com')
            ->to('touati.grombalia@gmail.com')
            ->subject('There is  a new reclamation')
            ->text('This User' . $reclam->getClient()->getId() . ' has  made a new reclamation')
            ->html('<p>This is a  reclamation about <b>' . $reclam->getSujet() . '</b> Here is a description' . $reclam->getDescription() . '</p>');

        $this->mailer->send($email);

        return new Response('Email sent!');
    }

    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();
            $this->sendEmail($reclamation);
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
    #[Route('/admin', name: 'app_admincharts_index', methods: ['GET'])]
    public function adminIndex(ReclamationRepository $reclamationRepository, ReponseRepository $reponseRepository): Response
    {
        // Fetch all reclamations
        $reclamations = $reclamationRepository->findAll();
        $reponses = $reponseRepository->findAll();

        $data = [];
        $data2 = [];

        // Count reclamations for each client
        foreach ($reclamations as $reclamation) {
            $clientId = $reclamation->getClient()->getId();

            if (!isset($data[$clientId])) {
                $data[$clientId] = [
                    'client' => $clientId,
                    'reclamation' => 0
                ];
            }

            $data[$clientId]['reclamation']++;
        }

        // Count responses for each reclamation
        foreach ($reponses as $rep) {
            $recId = $rep->getReclamation()->getId();

            if (!isset($data2[$recId])) {
                $data2[$recId] = [
                    'reclamation' => $recId,
                    'response' => 0 // Corrected 'reponse' to 'response'
                ];
            }

            $data2[$recId]['response']++;
        }

        return $this->render('admin.html.twig', [
            'data' => $data,
            'data2' => $data2,
        ]);
    }



    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}
