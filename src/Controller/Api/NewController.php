<?php


namespace App\Controller\Api;

use App\Document\Mongo\User;
use App\Repository\Mongo\UserRepository;
use App\Traits\ValidatorTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NewController extends AbstractController
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
     * @var DocumentManager
     */
    private $documentManager;
    /**
     * @var UserRepository
     */
    private $userRepository;
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

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
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

        $user       = $this->createNewUser($params);
        $token      = $this->JWTTokenManager->create($user);
        $response   = $this->getOKResponse(
            [
                'user'=>$user->getUserData(),
                'token' => $token
            ]);

        return $this->json($response, Response::HTTP_OK);

    }


    /**
     * @return Assert\Collection
     */
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


    /**
     * @param $params
     * @return User
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    private function createNewUser( $params ): User
    {
        $user = new User();
        $user->setName($params['name']);
        $user->setEmail($params['email']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $params['password']
        );
        $user->setPassword($hashedPassword);
        $this->documentManager->persist($user);
        $this->documentManager->flush();

        return  $user;

    }





}