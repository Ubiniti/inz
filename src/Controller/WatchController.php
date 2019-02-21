<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WatchController extends AbstractController
{
    /**
     * @Route("/watch/{id}", name="watch")
     */
    public function index()
    {
        return $this->render('watch/index.html.twig', [
            'controller_name' => 'WatchController',
        ]);
    }
}
