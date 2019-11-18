<?php

namespace App\Controller;

use App\Dto\RegistrationDto;
use App\Entity\Channel;
use App\Entity\User;
use App\Entity\Wallet;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use App\Services\Uploader\AvatarUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        EntityManagerInterface $em,
        AvatarUploader $uploader
    ): Response {
        $dto = new RegistrationDto();
        $form = $this->createForm(RegistrationFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $user = User::createFromDto($dto, $passwordEncoder, $uploader);
            $user->setWallet(new Wallet());
            $user->setChannel(new Channel($user->getUsername()));
            $user->setRoles($user->getRoles());
            $em->persist($user);
            $em->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
