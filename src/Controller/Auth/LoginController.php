<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{

    public function showLoginForm(){

        return $this->render('@twig_template/LoginPage.html.twig');
    }


}