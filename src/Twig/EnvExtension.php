<?php

namespace App\Twig;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EnvExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getenv', [$this, 'getenv']),
        ];
    }

    /**
     * @param string $parameter
     * @return array|false|string |null
     */
    public function getenv(string $parameter)
    {
        return getenv($parameter);
    }
}