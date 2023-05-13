<?php


namespace App\Controller\Auth;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
     public function showRegisterForm(){

        return $this->render('@twig_template/RegisterPage.html.twig');
    }

}