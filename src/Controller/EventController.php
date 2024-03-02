<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Stripe;

#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }
    #[Route('/front', name: 'app_event_index_front', methods: ['GET'])]
    public function indexfront(EventRepository $eventRepository): Response
    {
        return $this->render('event/indexFront.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('event_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $event->setImage($newFilename);
            }
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/front', name: 'app_event_show_front', methods: ['GET'])]
    public function showFront(Event $event): Response
    {
        return $this->render('event/showFront.html.twig', [
            'event' => $event,
        ]);
    }
    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('event_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $event->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/searchEvents', name: 'search_Events', methods: ['GET'])]
    public function searchEvents(Request $request, EventRepository $eventRepository): JsonResponse
    {
        // Retrieve the search query from the request
        $query = $request->query->get('query');

        // Perform the search operation based on the query
        $events = $eventRepository->searchBynom($query);
        
        // Return the search results as JSON response
        return $this->json($events);
    }
    #[Route('/{id}/donate', name: 'app_event_donate', methods: ['GET', 'POST'])]
    public function donate(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        return $this->render('event/stripe.html.twig', [
            'stripe_key' => 'pk_test_51OpE4kBxOYXVWsE1QqUrlXlwVtxifTmMkZtfRz4fDotwJHpyzDqoVX12RwROuedtl55vERhird31fYtK0Rhgo3PT008uq4mCV5',
        ]);
    }
    #[Route('/donate/charge', name: 'stripe_charge', methods: ['GET', 'POST'])]
    public function createCharge(Request $request)
    {
        Stripe\Stripe::setApiKey('sk_test_51OpE4kBxOYXVWsE1CZaTLF3Q3hXqeYfvwPeUpiptNBwBL66J2WosZ9B8AElaP5xxvF8HQJNC0ikhgw2mHdwjCwHy00OVSSJV1R');
        Stripe\Charge::create ([
                "amount" => 5 * 100,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('stripe', [], Response::HTTP_SEE_OTHER);
    }
}
