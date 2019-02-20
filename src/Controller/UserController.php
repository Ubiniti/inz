<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController {

    /**
     * @Route("/user", name="user")
     */
    public function index() {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('user/index.html.twig', [
                    'controller_name' => 'UserController',
                    'user' => $user,
        ]);
    }

    /**
     * @Route("/edit", name="user_edit")
     */
    public function editUser() {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('user/editUser.html.twig', [
                    'controller_name' => 'UserController',
                    'user' => $user,
        ]);
    }

    /**
     * @Route("/update", name="user_update")
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
                        'controller_name' => 'UserController',
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
     * @Route("/remove", name="user_remove")
     */
    public function removeUser() {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('user/removeUser.html.twig', [
                    'controller_name' => 'UserController',
                    'user' => $user,
        ]);
    }

    /**
     * @Route("/delete", name="user_delete")
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
                        'controller_name' => 'UserController',
                        'user' => $user,
                        'passMatch' => 'Provided password does not match the current password'
            ]);
        }
    }

}
