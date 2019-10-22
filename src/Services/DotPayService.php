<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DotPayService
{
    const DOTPAY_PAYMENT_URL = "https://ssl.dotpay.pl/test_payment/";
    const DOTPAY_SHOP_PIN = 'B226bZknPT8GsSCBtZEfvK2CkLTbR54B';
    const DOTPAY_SHOP_ID = '787372';

    /**
     * @var string
     */
    private $paymentUrl;
    /**
     * @var string
     */
    private $dotPayShopPin;
    /**
     * @var string
     */
    private $dotPayShopId;

    /**
     * DotPayService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        string $paymentUrl = null,
        string $dotPayShopPin = null,
        string $dotPayShopId = null
    )
    {
        $this->paymentUrl = self::DOTPAY_PAYMENT_URL;
        $this->dotPayShopPin = self::DOTPAY_SHOP_PIN;
        $this->dotPayShopId = self::DOTPAY_SHOP_ID;
    }

    /**
     * @param array $dotPayData
     * @return string
     */
    public function calculateChk(array $dotPayData): string
    {
        $data = '';
        foreach ($dotPayData as $value) {
            $data .= $value;
        }

        return hash("sha256", $data);
    }

    /**
     * @param string $shopPin
     * @param string $shopId
     * @param string $price
     * @param string $currency
     * @param string $description
     * @return array
     */
    public function generateParamsForBasicTransaction(
        string $url,
        string $urlc,
        string $price,
        string $currency,
        string $description
    ): array
    {
        $dotPay = [
            'pin' => $this->dotPayShopPin,
            'id' => $this->dotPayShopId,
            'kwota' => $price,
            'waluta' => $currency,
            'opis' => $description,
            'control' => $this->generatePaymentControlParam(),
        ];

        $dotPay['channel_groups'] = "K,T,P";
        $dotPay['url'] = $url;
        $dotPay['type'] = 0;
        $dotPay['urlc'] = $urlc;
        $dotPay['chk'] = $this->calculateChk($dotPay);

        return $dotPay;
    }

    /**
     * @param array $params
     * @return string|null
     */
    public function generateUrl(array $params): ?string
    {
        $queryString = http_build_query($params);
        return $this->paymentUrl . '?' . $queryString;
    }

    /**
     * @return string
     */
    public function generatePaymentControlParam(): string
    {
        return md5(time());
    }
}