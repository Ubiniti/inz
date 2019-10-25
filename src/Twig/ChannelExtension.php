<?php

namespace App\Twig;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ChannelExtension extends AbstractExtension
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
            new TwigFunction('getChannelName', [$this, 'getChannelName']),
        ];
    }

    public function getChannelName(string $username)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        $channelName = $user->getChannel()->getName();

        return $channelName;
    }
}