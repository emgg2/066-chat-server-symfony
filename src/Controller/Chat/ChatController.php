<?php

namespace App\Controller\Chat;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatController extends AbstractController
{

    public function showChatForm()
    {

        return $this->render('@twig_template/ChatPage.html.twig');
    }


}