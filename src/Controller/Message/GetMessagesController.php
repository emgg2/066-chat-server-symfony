<?php


namespace App\Controller\Message;

use App\Repository\Mongo\MessageRepository;
use App\Traits\JWTTrait;
use App\Traits\ValidatorTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GetMessagesController extends AbstractController
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
     * @var
     */
    private $message;

    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;

    /**
     * @var JWSProviderInterface
     */
    private $JWTProvider;
    /**
     * @var
     */
    private $errorMessage;


    use ValidatorTrait;
    use JWTTrait;

    public function __construct (
        ValidatorInterface $validator,
        DocumentManager $documentManager,
        MessageRepository $messageRepository,
        JWTTokenManagerInterface $JWTTokenManager,
        JWSProviderInterface $JWSProvider
    )
    {
        $this->validator = $validator;
        $this->documentManager = $documentManager;
        $this->message = $messageRepository;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->JWTProvider = $JWSProvider;

    }


    /**
     * @param Request $request
     * @param $messageFrom
     * @return Response
     */
    public function getMessages
    (
        Request  $request ,
        $messageFrom

    ) :Response
    {

        $token = $request->headers->get('x-token');
        $isValidJWT = $this->checkJWT( $token, $this->JWTProvider );
        if ( !$isValidJWT )
        {
            return  $this->json( $this->errorMessage,Response::HTTP_BAD_REQUEST );
        }

        $jwt = $this->JWTProvider->load($token);
        $payload = $jwt->getPayload();
        $myId   = $payload['uid'];

        $messages     = $this->message->getAllMessages($myId, $messageFrom);

        $response   = $this->getOKResponse(
            [
                'messages'=>$messages->getNewObj()
            ]);

        return $this->json($response, Response::HTTP_OK);

    }











}