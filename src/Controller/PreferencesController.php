<?php

namespace App\Controller;

use App\Form\PreferencesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreferencesController extends AbstractController
{
    /**
     * @Route("/preferences", name="app_user_preferences")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();

        $form = $this->createForm(PreferencesFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success', 'Zaktualizowano preferencje!');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $user->getChannel()->getName()]);
        }

        return $this->render('preferences/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
