<?php

namespace App\Controller;

use App\Form\UserEditFormType;
use App\Services\Uploader\AvatarUploader;
use App\Services\UserGetter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY", message="Brak dostępu.")
 * @Route("/user", name="app_user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function index() {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('user/index.html.twig', [
                    'user' => $user,
        ]);
    }

    /**
     * @Route("/edit", name="_edit")
     */
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        UserGetter $userGetter,
        AvatarUploader $avatarUploader
    ) {
        $user = $userGetter->get();

        $form = $this->createForm(UserEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('avatar')->getData()) {
                $user->setAvatar(
                    $avatarUploader->saveAvatar($form->get('avatar')->getData())
                );
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Zaktualizowano dane w profilu!');
        }

        return $this->render('user/edit_user.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update", name="_update")
     */
    public function updateUser(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        $credentials = [
            'username' => $request->request->get('username'),
            'birthdate' => $request->request->get('birthdate'),
            'email' => $request->request->get('email'),
            'country' => $request->request->get('country'),
            'currentPassword' => $request->request->get('currentPassword'),
            'newPassword' => $request->request->get('newPassword'),
        ];

        if ($encoder->isPasswordValid($user, $credentials['currentPassword']))
            $passwordMatch = true;
//        if ($encoder->encodePassword($user, $credentials['currentPassword']) != $user->getPassword())
//            $passwordMatch = false;
        else
            $passwordMatch = false;

        $user->setUsername($credentials['username']);
        $user->setEmail($credentials['email']);
        $user->setCountry($credentials['country']);
        $user->setBirthdate(new \DateTime($credentials['birthdate']));
        $user->setPassword($credentials['newPassword']);

        $errors = $validator->validate($user);

        if (count($errors) > 0 || !$passwordMatch)
        {

            return $this->render
                            ('user/editUser.html.twig', [
                        'user' => $user,
                        'errors' => $errors,
                        'passMatch' => 'Provided password does not match the current password'
            ]);
        }
        else
        {
            $user->setPassword($encoder->encodePassword($user, $credentials['newPassword']));
            $entityManager->flush();
            return $this->redirectToRoute('user');
        }
    }

    /**
     * @Route("/remove", name="_remove")
     */
    public function removeUser() {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('user/removeUser.html.twig', [
                    'user' => $user,
        ]);
    }

    /**
     * @Route("/delete", name="_delete")
     */
    public function deleteUser(Request $request, UserPasswordEncoderInterface $encoder) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        if ($encoder->isPasswordValid($user, $request->request->get('currentPassword')))
        {
            $this->get('security.token_storage')->setToken(null);
            $this->get('session')->invalidate();
            $entityManager->remove($user);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }
        else
        {
            return $this->render('user/removeUser.html.twig', [
                        'user' => $user,
                        'passMatch' => 'Provided password does not match the current password'
            ]);
        }
    }

}
