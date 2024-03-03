<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Form\AgenceType;
use App\Repository\AgenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontBackController extends AbstractController
{
    #[Route('/front', name: 'front', methods: ['GET'])]
    public function index(AgenceRepository $agenceRepository): Response
    {
        return $this->render('front.html.twig', [
            'agences' => $agenceRepository->findAll(),
        ]);
    }
    #[Route('/back', name: 'back', methods: ['GET'])]
    public function back(AgenceRepository $agenceRepository): Response
    {
        return $this->render('base.html.twig', [
            'agences' => $agenceRepository->findAll(),
        ]);
    }

    

}