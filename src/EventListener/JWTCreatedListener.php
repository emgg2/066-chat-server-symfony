<?php


namespace App\EventListener;

use App\Repository\Mongo\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;


class JWTCreatedListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, UserRepository $userRepository)
    {
        $this->requestStack = $requestStack;
        $this->userRepository = $userRepository;
    }
    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {

        $data = $event->getData();
        $user = $this->userRepository->findOneBy(["email"=> $data['username']]);
        $payload['uid'] = $user->getUserIdentifier();
        $event->setData($payload);

    }

}