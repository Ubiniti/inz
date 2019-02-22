<?php

namespace App\Controller;

use App\Entity\Video;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WatchController extends AbstractController
{
    /**
     * @Route("/watch/{id}", name="watch")
     */
    public function index($id)
    {
        //id = random string generated during upload
        //get full path from DB entry with corresponding id
        //get title,hashtags,comments,etc. from DB
        //a folder with .mp4 and comments.txt for each video
        
        $repository = $this->getDoctrine()->getRepository(Video::class);
        $video = $repository->findOneBy(['hash' => $id]);
        
        
        return $this->render('watch/index.html.twig', [
            'controller_name' => 'WatchController',
            'video' => $video->toArray()
            
        ]);
    }
    
    
}
