<?php


namespace App\Controller\Auth;

use App\Repository\Mongo\UserRepository;
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
    private $jwtProvider;
    private $errorMessage;
    private $userRepository;
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;

    use ValidatorTrait;

    public function __construct(
        ValidatorInterface $validator,
        JWSProviderInterface $jwtProvider,
        UserRepository $userRepository,
        JWTTokenManagerInterface $JWTTokenManager

    )
    {
        $this->validator = $validator;
        $this->constraints = $this->getConstraints();
        $this->jwtProvider = $jwtProvider;
        $this->userRepository = $userRepository;
        $this->JWTTokenManager = $JWTTokenManager;

    }
    public function renewToken(Request $request): Response
    {
        $token = $request->headers->get('x-token');
        $isValidJWT = $this->checkJWT( $token );
        if ( !$isValidJWT )
        {
            return  $this->json( $this->errorMessage,Response::HTTP_BAD_REQUEST );
        }
        $jwt = $this->jwtProvider->load($token);
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

    /**
     * @param string $token
     * @return bool
     */
    private function checkJWT( string $token ): bool
    {
        try {
            $jwt = $this->jwtProvider->load($token);
        } catch (\Exception $e) {
            $this->errorMessage = $this->getMessageErrorResponse($e->getMessage());
            return false;

        }

        if($jwt->isInvalid()){
            $this->errorMessage = $this->getMessageErrorResponse('Token Invalid');
            return false;

        }

        if($jwt->isExpired()){
            $this->errorMessage = $this->getMessageErrorResponse('Token has expired ');
            return  false;
        }


        return true;

    }


    private function getConstraints(): Assert\Collection
    {
        return  new Assert\Collection([

        ]);
    }



}