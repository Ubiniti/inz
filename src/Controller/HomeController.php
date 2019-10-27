<?php

namespace App\Controller;

use App\Entity\Category;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAllSortedByVideoCount();
//        dd($categories);
        return $this->render('home/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
