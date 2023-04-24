<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class AuthController extends AbstractController
{

    public function new() {
        return $this->json(
            [
               'ok'=>true,
                'user'=> 'register'

            ]
        );

    }
    public function login() {
        return $this->json(
            [
                'ok'=>true,
                'user'=> 'login'

            ]
        );

    }

    public function renewToken () {
        return $this->json(
            [
                'ok'=>true,
                'user'=> 'renewToken'

            ]
        );
    }
}