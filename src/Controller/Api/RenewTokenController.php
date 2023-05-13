<?php


namespace App\Controller\Api;

use App\Repository\Mongo\UserRepository;
use App\Traits\JWTTrait;
use App\Traits\ValidatorTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;


class RenewTokenController extends AbstractController
{
    private $validator;
    private $constraints;
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTProvider;
    private $errorMessage;
    private $userRepository;
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;

    use ValidatorTrait;
    use JWTTrait;

    public function __construct(
        ValidatorInterface $validator,
        JWSProviderInterface $JWTProvider,
        UserRepository $userRepository,
        JWTTokenManagerInterface $JWTTokenManager

    )
    {
        $this->validator = $validator;
        $this->constraints = $this->getConstraints();
        $this->JWTProvider = $JWTProvider;
        $this->userRepository = $userRepository;
        $this->JWTTokenManager = $JWTTokenManager;

    }
    public function renewToken(Request $request): Response
    {
        $token = $request->headers->get('x-token');
        $isValidJWT = $this->checkJWT( $token, $this->JWTProvider );
        if ( !$isValidJWT )
        {
            return  $this->json( $this->errorMessage,Response::HTTP_BAD_REQUEST );
        }
        $jwt = $this->JWTProvider->load($token);
        $payload = $jwt->getPayload();

        $user   = $this->userRepository->getUserById($payload['uid']);
        if ( !$user )
        {
            $this->errorMessage = $this->getMessageErrorResponse('User doesn´t exist');
            return  $this->json( $this->errorMessage,Response::HTTP_BAD_REQUEST );
        }

        $newToken  = $this->JWTTokenManager->create($user);


        $response   = $this->getOKResponse(
            [
                'user' => $user->getUserData(),
                'token' => $newToken
            ]);

        return $this->json( $response, Response::HTTP_OK );

    }


    private function getConstraints(): Assert\Collection
    {
        return  new Assert\Collection([

        ]);
    }



}