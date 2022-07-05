<?php

namespace App\Controller;

use App\Entity\Advert;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // $user = $this->getUser();
        // return $this->render('index/index.html.twig', [
        //     'controller_name' => 'IndexController',
        //     'user' => $user,
        // ]);

        $entityManager = $doctrine->getManager();

        $ads = $entityManager->getRepository(Advert::class)->findAll();

        return $this->render('index/index.html.twig', ['ads' => $ads]);
    }
}
