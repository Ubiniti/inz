<?php

namespace App\Controller;

use App\Entity\Channel;
use App\Repository\ChannelRepository;
use App\Services\UserGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChannelController extends AbstractController
{
    /**
     * @Route("/channel/{username}", name="app_user_channel")
     * @param Channel $channel
     * @return Response
     */
    public function index(string $username, ChannelRepository $channelRepository, UserGetter $userGetter)
    {
        $channel = $channelRepository->findOneBy([
            'user' => $userGetter->get()
        ]);

        return $this->render('channel/index.html.twig', [
            'channel' => $channel
        ]);
    }
}
