<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Form\AdFormType;
use App\Services\Uploader\AdUploader;
use App\Services\Uploader\Exception\FileFormatException;
use App\Services\Uploader\Exception\PathsJoinException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/advertisement", name="app_advertisement")
 * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
 */
class AdvertisementController extends AbstractController
{
    /**
     * @var AdUploader
     */
    private $adUploader;

    /**
     * AdvertisementController constructor.
     * @param AdUploader $adUploader
     */
    public function __construct(AdUploader $adUploader)
    {
        $this->adUploader = $adUploader;
    }

    /**
     * @Route("/add", name="_add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws FileFormatException
     * @throws PathsJoinException
     */
    public function add(Request $request, EntityManagerInterface $entityManager)
    {
        $ad = new Advertisement();
        $form = $this->createForm(AdFormType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ad->setContent($this->adUploader->saveContent($form->get('content')->getData()));
            $ad->setUser($this->getUser());
            $entityManager->persist($ad);
            $entityManager->flush();

            $this->addFlash('success', 'Dodano reklamę!');

            return $this->redirectToRoute('app_advertisement_ads');
        }

        return $this->render('advertisement/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="_remove")
     * @param Advertisement $advertisement
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function remove(Advertisement $advertisement, EntityManagerInterface $entityManager)
    {
        if ($advertisement->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Nie możesz usunąć reklamy, która nie należy do Ciebie.');

            return $this->redirectToRoute('app_advertisement_ads');
        }

        $entityManager->remove($advertisement);
        $entityManager->flush();

        $this->addFlash('success', 'Usunięto reklamę!');

        return $this->redirectToRoute('app_advertisement_ads');
    }

    /**
     * @Route("/{id}/pay", name="_pay")
     * @param Advertisement $advertisement
     * @return RedirectResponse
     */
    public function payForAd(Advertisement $advertisement, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();

        if ($advertisement->getUser() != $user) {
            $this->addFlash('error', 'Nie jesteś właścicielem tej reklamy i nie możesz za nią zapłacić.');

            return $this->redirectToRoute('app_user_channel', ['channel_name' => $this->getUser()->getChannel()->getName()]);
        }

        $wallet = $user->getWallet();
        $price = (int)getenv('ADVERTISEMENT_FIXED_PRICE');


        if ($wallet->getFunds() >= $price) {
            $wallet->setFunds($wallet->getFunds() - $price);
            $this->addFlash('sucess', 'Pobrano środki z Twojego portfela.');
            $advertisement->setIsPaidOff(true);
            $entityManager->flush();
        } else {
            $this->addFlash('error', 'Nie masz wystarczających środków w swoim portfelu.');

            return $this->redirectToRoute('app_user_wallet');
        }

        return $this->redirectToRoute('app_advertisement_ads');
    }

    /**
     * @Route("/", name="_ads")
     * @return Response
     */
    public function advertisements()
    {
        $ads = $this->getUser()->getAdvertisements();

        return $this->render('advertisement/index.html.twig', [
            'ads' => $ads
        ]);
    }
}
