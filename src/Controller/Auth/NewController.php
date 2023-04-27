<?php


namespace App\Controller\Auth;

use App\Traits\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;


class NewController extends AbstractController
{
    private $validator;
    private $util;
    private $constraints;

    use ValidatorTrait;

    public function __construct( ValidatorInterface $validator )
    {
        $this->validator = $validator;
        $this->constraints = $this->getConstraints();
    }
    public function new(Request  $request) {

        $params = $this->getParams( $request );
        $errors =  $this->validator->validate($params, $this->constraints );
        if( count($errors) >0 ) {
            $response = $this->getErrorResponse($errors);
            return  $this->json($response,Response::HTTP_BAD_REQUEST);
        }

        $response = $this->getOkResponse($params);

        return $this->json($response, Response::HTTP_OK);

    }
    private function getConstraints() : Assert\Collection
    {
        return  new Assert\Collection([
            'name' => [
                new Assert\NotBlank([
                    'message' => 'Name mandatory'
                ]),
            ],
            'email' => [
                new Assert\NotBlank([
                    'message' => 'Email mandatory'
                ]),
                new Assert\Email([
                    'message' => 'Email wrong'
                ])
            ],
            'password' => [
                new Assert\NotBlank([
                    'message' => 'Password mandatory'
                ]),
            ]
        ]);
    }

    private function getOkResponse ( $params ): array
    {
        $response = [
            "ok" => true,
            "pag" => 'register'
        ];

        foreach ($params as $param => $value )
        {
            $response[$param] =  $value ;
        }
        return $response;
    }

}