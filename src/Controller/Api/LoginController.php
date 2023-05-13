<?php

namespace App\Controller\Api;

use App\Repository\Mongo\UserRepository;
use App\Traits\ValidatorTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LoginController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Assert\Collection
     */
    private $constraints;

    /**
     * @var UserRepository
     */
    private $user;

    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;


    use ValidatorTrait;

    public function __construct(
        ValidatorInterface $validator,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $JWTTokenManager
    )
    {
        $this->validator = $validator;
        $this->constraints = $this->getConstraints();
        $this->user = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        $params  = $this->getParams( $request );
        $errors  = $this->validator->validate( $params, $this->constraints );
        if( count($errors) >0 ) {
            $response = $this->getErrorResponse( $errors );
            return  $this->json($response,Response::HTTP_BAD_REQUEST);
        }

        $user = $this->user->getUserByEmail( $params['email'] );

        if ( !$user ) {
            $response = $this->getMessageErrorResponse('User doesn´t exit');
            return  $this->json( $response,Response::HTTP_BAD_REQUEST );
        }

        $isPasswordValid = $this->passwordHasher->isPasswordValid( $user, $params['password'] );

        if ( !$isPasswordValid ) {
            $response = $this->getMessageErrorResponse('Invalid password');
            return  $this->json( $response,Response::HTTP_BAD_REQUEST );
        }

        $token      = $this->JWTTokenManager->create($user);
        $response   = $this->getOKResponse(
            [
                'user'=>$user->getUserData(),
                'token' => $token
            ]);

        return $this->json( $response, Response::HTTP_OK );

    }


    /**
     * @return Assert\Collection
     */
    private function getConstraints() : Assert\Collection
    {
       return  new Assert\Collection([
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email([
                    'message' => 'Email wrong'
                ])
            ],
            'password' => [
                new Assert\NotBlank(),
            ]
        ]);
    }





}