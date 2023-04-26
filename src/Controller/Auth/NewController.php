<?php


namespace App\Controller\Auth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constraints\ValidatorUtils;


class NewController extends AbstractController
{
    private $validator;
    private $util;


    public function __construct( ValidatorInterface $validator )
    {
        $this->validator = $validator;
        $this->util = new ValidatorUtils();
    }
    public function new(Request  $request) {
        $constraints = self::setConstraints();
        $params = $this->util->getParams( $request );
        $response = $this->util->validateParams( $params, $constraints, $this->validator );

        return $this->json($response['response'], $response['status']);

    }
    private function setConstraints() : Assert\Collection
    {
        return  new Assert\Collection([
        ]);
    }


}