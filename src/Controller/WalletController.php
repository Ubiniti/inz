<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Form\AddFinancesToWalletFormType;
use App\Services\DotPayService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class WalletController
 *
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
 * @Route("/wallet", name="app_user_wallet")
 */
class WalletController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function index()
    {
        $wallet = $this->getUser()->getWallet();

        if ($wallet === null) {
            $this->getUser()->setWallet(new Wallet());
        }

        return $this->render('wallet/index.html.twig', [
            'wallet' => $wallet
        ]);
    }

    /**
     * @Route("/buy/{amount}", name="_buy")
     * @param int $amount
     * @return Response
     */
    public function buyPoints(int $amount)
    {
        $wallet = $this->getUser()->getWallet();
        $wallet->setFunds($wallet->getFunds() + $amount);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new Response('OK');
    }

    /**
     * @Route("/buy-success", name="_buy_success")
     */
    public function buySuccess()
    {
        $this->addFlash('success', 'Zasilono portfel!');

        return $this->redirectToRoute('app_user_wallet');
    }

    /**
     * @Route("/add_finances", name="_add_finances")
     * @param Request $request
     * @param DotPayService $dotPayService
     * @return Response
     * @throws \Exception
     */
    public function addFinances(Request $request,  DotPayService $dotPayService)
    {
        $form = $this->createForm(AddFinancesToWalletFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dotPayParams =
                $dotPayService->generateParamsForBasicTransaction(
                    $this->generateUrl('app_user_wallet_buy_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    $this->generateUrl(
                        'app_user_wallet_buy', ['amount' => $form['amount']->getData()], UrlGeneratorInterface::ABSOLUTE_URL),

                    $form['amount']->getData()/100,
                    'PLN',
                    'Zasilenie portfela ' . (new DateTime())->format('d-m-Y') . ' kwota: ' .  $form['amount']->getData()
                );

            return $this->redirect($dotPayService->generateUrl($dotPayParams));
        }

        return $this->render('wallet/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
