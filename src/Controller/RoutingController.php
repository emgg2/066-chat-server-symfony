<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class RoutingController extends AbstractController
{

    public function editar($id) {

        return new Response(
            '<html><body><h1>Editando usurios con id:'.$id.'</h1></body>'
        );

    }
    public function listar($page, $title) {
        return new Response(

                phpinfo()
        );

    }

    public function exampleGenerateUrl () {
        $url = $this->generateUrl('user_list', ['page'=> 3, 'company' =>'TrainingIT'], UrlGeneratorInterface::ABSOLUTE_URL);
        dump($url);
    }
}