<?php

namespace App\Controller;

use App\Dto\VideoUploadFormDto;
use App\Entity\Playlist;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\VideoRate;

use App\Form\AddVideoToPlaylistFormType;
use App\Form\AddVideoType;
use App\Repository\CommentRepository;
use App\Repository\VideoRateRepository;
use App\Repository\VideoRepository;
use App\Services\VideoManager;
use App\Services\Uploader\VideoUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/video", name="app_video_")
 */
class VideoController extends AbstractController
{
    /**
     * @var VideoRepository
     */
    private $videoRepository;

    /**
     * @var VideoRateRepository
     */
    private $videoRateRepository;

    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var VideoManager
     */
    private $videoManager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(
        VideoRepository $videoRepository,
        VideoRateRepository $videoRateRepository,
        CommentRepository $commentRepository,
        VideoManager $videoManager,
        Security $security
    )
    {
        $this->videoRepository = $videoRepository;
        $this->videoRateRepository = $videoRateRepository;
        $this->commentRepository = $commentRepository;
        $this->videoManager = $videoManager;
        $this->security = $security;
    }

    /**
     * @Route("/{video_hash}", name="watch")
     */
    public function index(string $video_hash)
    {
        $user = $this->security->getUser();

        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);

        if ($video->getPrice() > 0 && $user->getChannel() !== $video->getChannel()) {

            return $this->redirectToRoute('app_video_watch_paid', ['video_hash' => $video_hash]);
        }

        $comments = $this->commentRepository->findBy(['video' => $video]);

        $this->videoManager->incrementViews($video);

        $thumbs_up = $this->videoRateRepository->countRate($video, VideoRate::UP);
        $thumbs_down = $this->videoRateRepository->countRate($video, VideoRate::DOWN);

        $rate = null;

        if ($user) {
            $videoRate = $this->videoRateRepository->findOneBy(['video' => $video, 'author' => $user]);

            if ($videoRate) {
                $rate = $videoRate->getRate();
            }
        }

        return $this->render('video/index.html.twig', [
            'video' => $video,
            'thumbs_up' => $thumbs_up,
            'thumbs_down' => $thumbs_down,
            'user_rate' => $rate,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/{video_hash}/paid", name="watch_paid")
     * @param string $video_hash
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function watchPaidVideo(string $video_hash, EntityManagerInterface $entityManager)
    {
        $user = $this->security->getUser();

        if ($user === null) {
            $this->addFlash('error', 'Musisz się zalogować, aby mieć dostęp do swojego Portfela.');

            return $this->redirectToRoute('app_login');
        }

        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);

        if (!($user->getPaidForVideos()->contains($video)) && $user->getChannel() !== $video->getChannel()) {
            $wallet = $user->getWallet();
            if ($wallet->getFunds() >= $video->getPrice()) {
                $wallet->setFunds($wallet->getFunds() - $video->getPrice());
                $this->addFlash('sucess', 'Pobrano środki z Twojego portfela.');
                $user->addPaidForVideo($video);
                $entityManager->flush();
            } else {
                $this->addFlash('error', 'Nie masz wystarczających środków w swoim portfelu.');

                return $this->redirectToRoute('app_user_wallet');
            }
        }

        $comments = $this->commentRepository->findBy(['video' => $video]);
        $this->videoManager->incrementViews($video);

        $thumbs_up = $this->videoRateRepository->countRate($video, VideoRate::UP);
        $thumbs_down = $this->videoRateRepository->countRate($video, VideoRate::DOWN);

        $rate = null;

        if ($user) {
            $videoRate = $this->videoRateRepository->findOneBy(['video' => $video, 'author' => $user]);

            if ($videoRate) {
                $rate = $videoRate->getRate();
            }
        }

        return $this->render('video/index.html.twig', [
            'video' => $video,
            'thumbs_up' => $thumbs_up,
            'thumbs_down' => $thumbs_down,
            'user_rate' => $rate,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/{video_hash}/rate", methods={"POST"}, name="rate")
     */
    public function rate(string $video_hash, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $videoRateRepo = $entityManager->getRepository(VideoRate::class);

        $rate = $request->request->get('rate');
        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);

        $existingRate = $videoRateRepo->findOneBy([
            'video' => $video,
            'author' => $user->getUsername()
        ]);

        if ($existingRate) {
            $existingRate->setRate($rate);
            $entityManager->persist($existingRate);
        } else {
            $videoRate = new VideoRate();
            $videoRate->setVideo($video);
            $videoRate->setAuthor($user->getUsername());
            $videoRate->setRate($rate);
            $entityManager->persist($videoRate);
        }

        $entityManager->flush();

        return $this->redirectToRoute('video_get_rate', [
            'video_hash' => $video_hash,
            'positive' => $rate
        ]);
    }

    /**
     * @Route("/", name="add")
     * @param VideoUploader $uploader
     * @param Request $request
     * @return Response
     */
    public function add(VideoUploader $uploader, Request $request)
    {
        $dto = new VideoUploadFormDto();
        $form = $this->createForm(AddVideoType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploader->saveVideo($dto);
        }

        return $this->render('video/add.html.twig', [
            'success_route' => 'home'
        ]);
    }

    /**
     * @Route("/remove/{video_hash}", name="remove")
     * @param string $video_hash
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function removeVideo(string $video_hash, EntityManagerInterface $entityManager)
    {
        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);

        if ($this->getUser()->getChannel() != $video->getChannel()) {
            $this->addFlash('error', 'Podany film nie należy do Twojego kanału.');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        $entityManager->remove($video);
        $entityManager->flush();
        $this->addFlash('success', 'Usunięto film!');

        return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
    }

    /**
     * @Route("/{video_hash}/rate", methods={"GET"}, name="get_rate")
     */
    public function getRate(string $video_hash)
    {
        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);

        $countThumbsUp = $this->videoRateRepository->countRate($video, VideoRate::UP);
        $countThumbsDown = $this->videoRateRepository->countRate($video, VideoRate::DOWN);


        $rate = [
            "up" => $countThumbsUp,
            "down" => $countThumbsDown
        ];

        $result = json_encode($rate);

        return new Response($result);
    }

    /**
     * @Route("/{video_hash}/add-to-playlist", methods={"POST", "GET"}, name="add_to_playlist")
     * @param string $video_hash
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addVideoToPlaylist(string $video_hash, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(AddVideoToPlaylistFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlist = $form->get('playlist')->getData();

            if ($this->getUser() != $playlist->getChannel()->getUser()) {
                $this->addFlash('error', 'Podana playlista nie należy do Twojego kanału.');

                return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
            }

            $playlist->addVideo($this->videoRepository->findOneBy(['hash' => $video_hash]));

            $entityManager->flush();
            $this->addFlash('success', 'Dodano film do playlisty!');

            return $this->redirectToRoute('app_playlist', ['id' => $playlist->getId()]);
        }

        return $this->render('video/add_to_playlist.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{video_hash}/remove-from-playlist/{playlist}", name="remove_from_playlist")
     * @param string $video_hash
     * @param Playlist $playlist
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function removeVideoFromPlaylist(string $video_hash, Playlist $playlist, EntityManagerInterface $entityManager)
    {
        if ($this->getUser() != $playlist->getChannel()->getUser()) {
            $this->addFlash('error', 'Podana playlista nie należy do Twojego kanału.');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        $playlist->removeVideo($this->videoRepository->findOneBy(['hash' => $video_hash]));
        $entityManager->flush();
        $this->addFlash('success', 'Usunięto film z playlisty!');

        return $this->redirectToRoute('app_playlist', ['id' => $playlist->getId()]);
    }
}
