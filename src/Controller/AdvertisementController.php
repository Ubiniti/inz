<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Form\AdFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/advertisement", name="app_advertisement")
 */
class AdvertisementController extends AbstractController
{
    /**
     * @Route("/add", name="_add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager)
    {
        $ad = new Advertisement();
        $form = $this->createForm(AdFormType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ad);
            $entityManager->flush();
        }

        return $this->render('advertisement/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/pay", name="_pay")
     * @param Advertisement $advertisement
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

        return $this->redirectToRoute('app_user_ads');
    }

    /**
     * @Route("/", name="_ads")
     * @return Response
     */
    public function advertisements()
    {
       $ads = $this->getUser()->getAdvertisements();

        return $this->render('advertisement/add.html.twig', [
            'ads' => $ads
        ]);
    }
}
