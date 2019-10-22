<?php

namespace App\Controller;

use App\Entity\Wallet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PlaylistController
 *
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
 * @Route("/playlist", name="app_playlist_")
 */
class PlaylistController extends AbstractController
{
    /**
     * @Route("/add", name="add")
     */
    public function addPlaylist()
    {

        return $this->render('playlist/add.html.twig', [
        ]);
    }
}
