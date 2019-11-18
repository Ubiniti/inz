<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddVideoToPlaylistFormType extends AbstractType
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $builder
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'data' => $user->getChannel()->getPlaylists(),
                'choice_label' => 'title',
                'required' => true,
                'label' => 'Playlista'
            ])
        ;
    }
}
