<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        
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
    public function editUser()
    {
        
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
    public function updateUser(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    {
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $credentials = [
            'username' => $request->request->get('username'),
            'birthdate' => $request->request->get('birthdate'),
            'email' => $request->request->get('email'),
            'country' => $request->request->get('country'),
            'password' => $request->request->get('password'),
        ];
        
        $user->setUsername($credentials['username']);
        $user->setEmail($credentials['email']);
        $user->setCountry($credentials['country']);
        $user->setBirthdate(new \DateTime($credentials['birthdate']));
        $user->setPassword($credentials['password']);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {

            return $this->render
                            ('user/editUser.html.twig', [
                        'controller_name' => 'UserController',
                        'user' => $user,
                        'errors' => $errors
            ]);
        } else {
            $user->setPassword($encoder->encodePassword($user, $credentials['password']));
            $entityManager->flush();
            return $this->redirectToRoute('user');
        }

    }
}
