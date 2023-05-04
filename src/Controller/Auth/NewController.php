<?php


namespace App\Controller\Auth;

use App\Document\Mongo\User;
use App\Repository\Mongo\UserRepository;
use App\Traits\ValidatorTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NewController extends AbstractController
{
    private $validator;
    private $constraints;
    private $documentManager;
    private $userRepository;
    private $passwordHasher;
    private $eventDispatcher;
    private $JWTTokenManager;


    use ValidatorTrait;

    public function __construct(
        ValidatorInterface $validator,
        DocumentManager $documentManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $JWTTokenManager
    )
    {
        $this->validator = $validator;
        $this->constraints = $this->getConstraints();
        $this->documentManager = $documentManager;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    public function new(
        Request  $request
        ) {
        $params = $this->getParams( $request );

        $errors =  $this->validator->validate($params, $this->constraints );

        if( count($errors) >0 ) {
            $response = $this->getErrorResponse($errors);
            return  $this->json($response,Response::HTTP_BAD_REQUEST);
        }

        if($this->userRepository->isEmailExists($params['email']))
        {
            $response = $this->getMessageResponse('Email already exists');
            return $this->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = $this->createNewUser($params);
        $response = $this->getUserData($user);

        return $this->json($response, Response::HTTP_OK);

    }



    private function getConstraints() : Assert\Collection
    {
        return  new Assert\Collection([
            'name' => [
                new Assert\NotBlank(),
            ],
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

    private function getUserData ( $user ): array
    {
        $token = $this->getTokenUser($user);

        return  [
            "user" => $user->getUserData(),
            "token"     => $token

        ];
    }

    /**
     * @param $params
     *
     * @return string
     */

    private function createNewUser( $params ): User
    {
        $user = new User();
        $user->setName($params['name']);
        $user->setEmail($params['email']);
        $plaintextPassword = $this->getParameter('app.secret_hash');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $this->documentManager->persist($user);
        $this->documentManager->flush();

        return  $user;

    }

    public function getTokenUser ( UserInterface $user)
    {
        return $this->JWTTokenManager->create($user);
    }



}