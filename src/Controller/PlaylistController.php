<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PlaylistController
 *
 * @package App\Controller
 * @Route("/playlist", name="app_playlist")
 */
class PlaylistController extends AbstractController
{
    /**
     * @Route("/add", name="_add")
     * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addPlaylist(Request $request, EntityManagerInterface $entityManager)
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistFormType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlist->setChannel($this->getUser()->getChannel());
            $entityManager->persist($playlist);
            $entityManager->flush();
            $this->addFlash('success', 'Dodano playlistę!');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        return $this->render('playlist/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="_edit")
     * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
     * @param Playlist $playlist
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editPlaylist(Playlist $playlist, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->getUser()->getChannel() != $playlist->getChannel()) {
            $this->addFlash('error', 'Podana playlista nie należy do Twojego kanału.');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        $form = $this->createForm(PlaylistFormType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Edytowano playlistę!');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        return $this->render('playlist/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/remove/{id}", name="_remove")
     * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
     * @param Playlist $playlist
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function removePlaylist(Playlist $playlist, EntityManagerInterface $entityManager)
    {
        if ($this->getUser()->getChannel() != $playlist->getChannel()) {
            $this->addFlash('error', 'Podana playlista nie należy do Twojego kanału.');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        $entityManager->remove($playlist);
        $entityManager->flush();
        $this->addFlash('success', 'Usunięto playlistę!');

        return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
    }

    /**
     * @Route("/{id}", name="")
     * @param Playlist $playlist
     * @return Response
     */
    public function playlistView(Playlist $playlist)
    {
        if ($playlist->getIsPublic() === false && $playlist->getChannel()->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Podana playlista jest prywatna.');

            return $this->redirectToRoute('home');
        }

        return $this->render('playlist/index.html.twig', [
            'playlist' => $playlist
        ]);
    }
}
