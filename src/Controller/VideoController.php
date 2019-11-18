<?php

namespace App\Controller;

use App\Dto\VideoUploadFormDto;
use App\Entity\Advertisement;
use App\Entity\Playlist;
use App\Entity\User;
use App\Entity\VideoRate;

use App\Form\AddVideoToPlaylistFormType;
use App\Form\AddVideoType;
use App\Form\EditVideoFormType;
use App\Repository\CommentRepository;
use App\Repository\VideoRateRepository;
use App\Repository\VideoRepository;
use App\Services\UserGetter;
use App\Services\VideoEditor;
use App\Services\VideoManager;
use App\Services\Uploader\VideoUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(
        VideoRepository $videoRepository,
        VideoRateRepository $videoRateRepository,
        CommentRepository $commentRepository,
        VideoManager $videoManager,
        EntityManagerInterface $entityManager,
        Security $security
    )
    {
        $this->videoRepository = $videoRepository;
        $this->videoRateRepository = $videoRateRepository;
        $this->commentRepository = $commentRepository;
        $this->videoManager = $videoManager;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/{video_hash}", name="watch", requirements={"video_hash"="[\w\d]{32}"})
     * @param string $video_hash
     * @return RedirectResponse|Response
     */
    public function index(string $video_hash)
    {
        $user = $this->security->getUser();

        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);

        if ($video->getPrice() > 0 && $user->getChannel() !== $video->getChannel()) {

            return $this->redirectToRoute('app_video_watch_paid', ['video_hash' => $video_hash]);
        }

        $comments = $this->commentRepository->findBy(['video' => $video, 'parent' => null]);

        $this->videoManager->incrementViews($video);

        $ad = null;

        if ($video->getAllowsAds() === true) {
            $ads = $this->getDoctrine()->getRepository(Advertisement::class)->findAll();
            shuffle($ads);

            foreach ($ads as $a) {
                if ($a->getIsPaidOff()) {
                    $ad = $a;
                }
            }

            if ($ad !== null) {
                $ad->setViews($ad->getViews() + 1);
                $wallet = $ad->getUser()->getWallet();
                if ($wallet->getFunds() >= getenv('ADVERTISEMENT_PRICE_PER_VIEW')) {
                    $wallet->setFunds($wallet->getFunds() - getenv('ADVERTISEMENT_PRICE_PER_VIEW'));
                    $videoOwnerWallet = $video->getChannel()->getUser()->getWallet();
                    $videoOwnerWallet->setFunds($videoOwnerWallet->getFunds() + (getenv('ADVERTISEMENT_PRICE_PER_VIEW')/2));
                } else {
                    $ad->setIsPaidOff(false);
                }
                $this->entityManager->flush();
            }
        }

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
            'comments' => $comments,
            'ad' => $ad
        ]);
    }

    /**
     * @Route("/{video_hash}/demo", name="watch_demo")
     * @param string $video_hash
     * @return RedirectResponse|Response
     */
    public function watchDemo(string $video_hash)
    {
        $user = $this->security->getUser();
        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);
        $comments = $this->commentRepository->findBy(['video' => $video, 'parent' => null]);

        $ad = null;

        if ($video->getAllowsAds() === true) {
            $ads = $this->getDoctrine()->getRepository(Advertisement::class)->findAll();
            shuffle($ads);

            foreach ($ads as $a) {
                if ($a->getIsPaidOff()) {
                    $ad = $a;
                }
            }

            if ($ad !== null) {
                $ad->setViews($ad->getViews() + 1);
                $wallet = $ad->getUser()->getWallet();
                if ($wallet->getFunds() >= getenv('ADVERTISEMENT_PRICE_PER_VIEW')) {
                    $wallet->setFunds($wallet->getFunds() - getenv('ADVERTISEMENT_PRICE_PER_VIEW'));
                    $videoOwnerWallet = $video->getChannel()->getUser()->getWallet();
                    $videoOwnerWallet->setFunds($videoOwnerWallet->getFunds() + (getenv('ADVERTISEMENT_PRICE_PER_VIEW')/2));
                } else {
                    $ad->setIsPaidOff(false);
                }
                $this->entityManager->flush();
            }
        }

        return $this->render('video/demo.html.twig', [
            'video' => $video,
            'comments' => $comments,
            'ad' => $ad
        ]);
    }

    /**
     * @Route("/{video_hash}/paid", name="watch_paid")
     * @param string $video_hash
     * @return RedirectResponse|Response
     */
    public function watchPaidVideo(string $video_hash)
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
                $this->entityManager->flush();
            } else {
                $this->addFlash('error', 'Nie masz wystarczających środków w swoim portfelu.');

                return $this->redirectToRoute('app_user_wallet');
            }
        }

        $comments = $this->commentRepository->findBy(['video' => $video]);
        $this->videoManager->incrementViews($video);

        $thumbsUp = $this->videoRateRepository->countRate($video, VideoRate::UP);
        $thumbsDown = $this->videoRateRepository->countRate($video, VideoRate::DOWN);

        $rate = null;

        if ($user) {
            $videoRate = $this->videoRateRepository->findOneBy(['video' => $video, 'author' => $user]);

            if ($videoRate) {
                $rate = $videoRate->getRate();
            }
        }

        return $this->render('video/index.html.twig', [
            'video' => $video,
            'thumbs_up' => $thumbsUp,
            'thumbs_down' => $thumbsDown,
            'user_rate' => $rate,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/{video_hash}/rate", methods={"POST"}, name="rate")
     * @param string $video_hash
     * @param Request $request
     * @return RedirectResponse
     */
    public function rate(string $video_hash, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();

        $videoRateRepo = $this->entityManager->getRepository(VideoRate::class);

        $rate = $request->request->get('rate');
        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);

        $existingRate = $videoRateRepo->findOneBy([
            'video' => $video,
            'author' => $user->getUsername()
        ]);

        if ($existingRate) {
            $existingRate->setRate($rate);
            $this->entityManager->persist($existingRate);
        } else {
            $videoRate = new VideoRate();
            $videoRate->setVideo($video);
            $videoRate->setAuthor($user->getUsername());
            $videoRate->setRate($rate);
            $this->entityManager->persist($videoRate);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('app_video_get_rate', [
            'video_hash' => $video_hash,
            'positive' => $rate
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"POST", "GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
     * @param VideoUploader $uploader
     * @param Request $request
     * @return Response
     */
    public function add(VideoUploader $uploader, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $dto = new VideoUploadFormDto();
        $form = $this->createForm(AddVideoType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploader->saveVideo($dto);
        }

        return $this->render('video/add_v2.html.twig', [
            'success_route' => 'app_user_channel',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/add/watermark-status", name="upload_conversion_status", methods={"POST", "GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
     * @param VideoUploader $uploader
     * @param Request $request
     * @return Response
     */
    public function watermarkStatus(VideoUploader $uploader, UserGetter $userGetter, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $progress = VideoEditor::getConversionProgress($userGetter->getUsername());

        return $this->json($progress);
    }

    /**
     * @Route("/{video_hash}/edit", name="edit")
     * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
     * @param Request $request
     * @return Response
     */
    public function edit(string $video_hash, Request $request)
    {
        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);
//        $dto = VideoEditFormDto::createFromEntity($video);
        /** @var User $user */
        $user = $this->getUser();

        if ($video->getChannel()->getUser() !== $user) {
            $this->addFlash('error', 'Nie jesteś autorem tego filmu i nie możesz go edytować.');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        $form = $this->createForm(EditVideoFormType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        return $this->render('video/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/remove/{video_hash}", name="remove")
     * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
     * @param string $video_hash
     * @return Response
     */
    public function removeVideo(string $video_hash)
    {
        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);

        if ($this->getUser()->getChannel() != $video->getChannel()) {
            $this->addFlash('error', 'Podany film nie należy do Twojego kanału.');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        $this->entityManager->remove($video);
        $this->entityManager->flush();
        $this->addFlash('success', 'Usunięto film!');

        return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
    }

    /**
     * @Route("/{video_hash}/rate", methods={"GET"}, name="get_rate")
     * @param string $video_hash
     * @return Response
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
     * @return Response
     */
    public function addVideoToPlaylist(string $video_hash, Request $request)
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

            $this->entityManager->flush();
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
     * @return Response
     */
    public function removeVideoFromPlaylist(string $video_hash, Playlist $playlist)
    {
        if ($this->getUser() != $playlist->getChannel()->getUser()) {
            $this->addFlash('error', 'Podana playlista nie należy do Twojego kanału.');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        $playlist->removeVideo($this->videoRepository->findOneBy(['hash' => $video_hash]));
        $this->entityManager->flush();
        $this->addFlash('success', 'Usunięto film z playlisty!');

        return $this->redirectToRoute('app_playlist', ['id' => $playlist->getId()]);
    }
}
