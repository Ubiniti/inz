<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
}
