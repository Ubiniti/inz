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
        //id = hashed path
        //get full path from DB entry with corresponding id
        //get title,hashtags,etc. from DB
        
        $path="grill";
        $title="grill";
        
        return $this->render('watch/index.html.twig', [
            'controller_name' => 'WatchController',
            'path' => $path,
            'title' => $title
        ]);
    }
}
