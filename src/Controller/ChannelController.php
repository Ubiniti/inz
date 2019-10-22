<?php

namespace App\Controller;

use App\Entity\Channel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChannelController extends AbstractController
{
    /**
     * @Route("/channel/{id}", name="app_user_channel")
     * @param Channel $channel
     * @return Response
     */
    public function index(Channel $channel)
    {
        return $this->render('channel/index.html.twig', [
            'channel' => $channel
        ]);
    }
}
