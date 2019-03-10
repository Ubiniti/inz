<?php

namespace App\Controller;

use App\Entity\Video;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Video::class);
        $videos = $repository->findBy([], ['views' => 'DESC'], 20);
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'videos' => $videos
        ]);
    }
}
