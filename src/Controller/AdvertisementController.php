<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Form\AdFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/advertisement", name="app_advertisement")
 */
class AdvertisementController extends AbstractController
{
    /**
     * @Route("/add", name="_add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager)
    {
        $ad = new Advertisement();
        $form = $this->createForm(AdFormType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ad);
            $entityManager->flush();
        }

        return $this->render('advertisement/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
