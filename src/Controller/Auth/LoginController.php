<?php


namespace App\Controller\Auth;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constraints\ValidatorUtils;
use Symfony\Component\HttpFoundation\Response as Response;

class LoginController extends AbstractController
{
    private $validator;
    private $util;
    private $constraints;


    public function __construct( ValidatorInterface $validator )
    {
        $this->validator = $validator;
        $this->util = new ValidatorUtils();
        $this->constraints = self::getConstraints();

    }


    public function login(Request $request)
    {

        $params = $this->util->getParams( $request );
        $errors =  $this->validator->validate($params, $this->constraints );
        if( count($errors) >0 ) {
            $response = $this->util->getErrorResponse($errors);
            return  $this->json($response,Response::HTTP_BAD_REQUEST);
        }

        $response = self::getOkResponse($params);

        return $this->json($response, Response::HTTP_OK);

    }


    private function getConstraints() : Assert\Collection
    {
       return  new Assert\Collection([
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
            "pag" => 'login'
        ];

        foreach ($params as $param => $value )
        {
            $response[$param] =  $value ;
        }
        return $response;
    }




}