<?php

namespace App\Controller;

use App\Dto\VideoUploadFormDto;
use App\Entity\User;
use App\Entity\VideoRate;

use App\Form\AddVideoType;
use App\Repository\CommentRepository;
use App\Repository\VideoRateRepository;
use App\Repository\VideoRepository;
use App\Services\VideoManager;
use App\Services\Uploader\VideoUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/video", name="video_")
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
    ) {
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
        $comments = $this->commentRepository->findBy(['video' => $video]);

        $this->videoManager->incrementViews($video);

        $thumbs_up = $this->videoRateRepository->countRate($video, VideoRate::UP);
        $thumbs_down = $this->videoRateRepository->countRate($video, VideoRate::DOWN);
        
        $rate = null;

        if($user) {
            $videoRate = $this->videoRateRepository->findOneBy(['video' => $video, 'author' => $user]);

            if($videoRate) {
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

        if($existingRate)
        {
            $existingRate->setRate($rate);
            $entityManager->persist($existingRate);
        }
        else
        {
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
     */
    public function add(VideoUploader $uploader, Request $request)
    {
        $dto = new VideoUploadFormDto();
        $form = $this->createForm(AddVideoType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploader->saveVideo($dto);
        }

        return $this->render('video/add.html.twig');
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
}
