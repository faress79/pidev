<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Form\AgenceType;
use App\Repository\AgenceRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use UltraMsg\WhatsAppApi;

#[Route('/agence')]
class AgenceController extends AbstractController
{
    #[Route('/agence/{id}/dislike', name: 'app_agence_dislike')]
    public function dislikeBadge(Agence $agence): Response
    {
        $agence->incrementDislikes();

        if ($agence->checkAndDeleteIfRequired()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($agence);
            $entityManager->flush();

            return $this->redirectToRoute('app_agence_indexf');
        } else {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_agence_indexf');
        }
    }
    #[Route('/', name: 'app_agence_index', methods: ['GET'])]
    public function index(AgenceRepository $agenceRepository): Response
    {
        return $this->render('agence/index.html.twig', [
            'agences' => $agenceRepository->findAll(),
        ]);
    }
    #[Route('/front', name: 'app_agence_indexf', methods: ['GET'])]
    public function indexfro(AgenceRepository $agenceRepository): Response
    {
        return $this->render('agence/indexf.html.twig', [
            'agences' => $agenceRepository->findAll(),
        ]);
    }

    //composer require ultramsg/whatsapp-php-sdk
    #[Route('/what', name: 'whatsapp')]
    public function envoyerMessageWhatsApp($agence, $email, $date): Response
    {
        require_once __DIR__ . '/../../vendor/autoload.php'; // Make sure the path is correct
        $ultramsg_token = "xlvw4dz9wcdxk5pi"; // Your Ultramsg token
        $instance_id = "instance69768"; // Your Ultramsg instance ID
    
        $client = new WhatsAppApi($ultramsg_token, $instance_id);
    
        $to = "+21622305479"; // Recipient's phone number
        $body = "Bonjour,\n\nNous vous informons qu'un nouveau agence a été ajouté dans notre système. Voici les détails :\n\nUtilisateur  : $agence\nemail : $email\nphone number : $date\n\nVeuillez prendre les mesures nécessaires pour suivi les mise a jour.\n\nCordialement.";
    
        // Send a text message
        $api = $client->sendChatMessage($to, $body);
    
        // Send an image message
        $image = "https://www.1jour1actu.com/wp-content/uploads/2017/06/01-sinformer.jpg";
        $caption = "Image Caption";
        $priority = 10;
        $referenceId = "SDK";
        $nocache = false;
        $imageApi = $client->sendImageMessage($to, $image, $caption, $priority, $referenceId, $nocache);
    
        print_r($api); // Handle the response as needed for the text message
        print_r($imageApi); // Handle the response for the image message
    
        // You can manage the responses as desired, for example, display them
        return new Response('WhatsApp messages sent successfully!');
    }
    
   

    #[Route('/agence/{id}/like', name: 'app_agence_like')]
    public function likeBadge(Agence $agence): Response
    {
        $agence->incrementLikes();
        // Enregistrez les modifications dans la base de données
        $this->getDoctrine()->getManager()->flush();
    
        return $this->redirectToRoute('app_agence_indexf');
    }

    #[Route('/new', name: 'app_agence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where your images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('img_directory'), // specify the directory where images should be stored
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle the exception if something happens during the file upload
                }

                // Update the 'image' property to store the file name instead of its contents
                $agence->setImage($newFilename);
            }
            $entityManager->persist($agence);
            $entityManager->flush();
            $agen = $agence->getNom(); // Remplacez par la méthode réelle pour obtenir le nom du restaurant
            $email = $agence->getEmail();
            $date = $agence->getTel(); // Formatage de la date, remplacez selon votre format
        
            $this->envoyerMessageWhatsApp( $agen, $email, $date);
            return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agence/new.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_agence_show', methods: ['GET'])]
    public function show(Agence $agence): Response
    {
        return $this->render('agence/show.html.twig', [
            'agence' => $agence,
        ]);
    }
    #[Route('front/{id}', name: 'app_agence_showf', methods: ['GET'])]
    public function showfront(Agence $agence,EntityManagerInterface $entityManager ): Response
    {
        $currentViews = $agence->getNbVue();
        $agence->setNbVue($currentViews + 1);

        $entityManager->persist($agence);
            $entityManager->flush();
        return $this->render('agence/showf.html.twig', [
            'agence' => $agence,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_agence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Agence $agence, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where your images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('img_directory'), // specify the directory where images should be stored
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle the exception if something happens during the file upload
                }

                // Update the 'image' property to store the file name instead of its contents
                $agence->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agence/edit.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_agence_delete', methods: ['POST'])]
    public function delete(Request $request, Agence $agence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $agence->getId(), $request->request->get('_token'))) {
            $entityManager->remove($agence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
    }
}
