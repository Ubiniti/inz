<?php

namespace App\Controller;

use App\Entity\Wallet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WalletController
 *
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
 */
class WalletController extends AbstractController
{
    /**
     * @Route("/wallet", name="wallet")
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
}
