<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdvertController extends AbstractController
{
    #[Route('/adverts/add', name: 'app_advert_add')]
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $advert = new Advert();

        $form  = $this->createForm(AdvertType::class);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $advert->setUser($this->getUser());
            $advert->setTitle($data->getTitle());
            $advert->setPrice($data->getPrice());
            $advert->setDescription($data->getDescription());
            $advert->setCreatedAt(new \DateTimeImmutable('now'));
            $advert->setUpdatedAt(new \DateTimeImmutable('now'));

            $entityManager->persist($advert);

            $entityManager->flush();

            return $this->redirectToRoute('app_index');
        }
        
        return $this->renderForm('advert/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/adverts/view/{id}', name: 'app_advert_view')]
    public function view($id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $advert = $entityManager->getRepository(Advert::class)->find($id);

        return $this->render('advert/view.html.twig', [
            'advert' => $advert
        ]);
    }

    #[Route('/adverts/edit/{id}', name: 'app_advert_edit')]
    public function edit($id, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $myAdvert = $entityManager->getRepository(Advert::class)->find($id);

        if (!$myAdvert) {
            throw $this->createNotFoundException('No advert found for id ' . $id);
        }
        
        $advert = new Advert();

        $advert->setTitle($myAdvert->getTitle());
        $advert->setPrice($myAdvert->getPrice());
        $advert->setDescription($myAdvert->getDescription());

        $form  = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $myAdvert->setTitle($data->getTitle());
            $myAdvert->setPrice($data->getPrice());
            $myAdvert->setDescription($data->getDescription());
            $myAdvert->setUpdatedAt(new \DateTimeImmutable('now'));

            $entityManager->flush();

            return $this->redirectToRoute('app_advert_view', [
                'id' => $myAdvert->getId(),
            ]);
        }

        return $this->renderForm('advert/edit.html.twig', [
            'form' => $form,
            'controller_name' => 'AdvertController',
        ]);
    }

    #[Route('/adverts/delete/{id}', name: 'app_advert_delete')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $advertTarget = $entityManager->getRepository(Advert::class)->find($id);

        $entityManager->getRepository(Advert::class)->remove($advertTarget);

        $entityManager->flush();

        return $this->redirectToRoute('app_index');
    }
}
