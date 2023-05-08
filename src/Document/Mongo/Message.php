<?php
namespace App\Document\Mongo;


use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(collection="Message", repositoryClass="App\Repository\Mongo\MessageRepository")
 * @MongoDB\HasLifecycleCallbacks
 */

// If you want the User object to be serialized to the session, you need to implement Serializable
// https://symfony.com/doc/current/security/entity_provider.html#what-do-the-serialize-and-unserialize-methods-do
class Message
{

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument=User::class)
     * @Assert\NotBlank()
     */
    protected $from;

    /**
     * @MongoDB\ReferenceOne (targetDocument=User::class)
     * @Assert\NotBlank()
     */
    protected $to;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $message;

    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     */
    public function setFrom(User $from): void
    {
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     */
    public function setTo(User $to): void
    {
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }



    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }


    public function serialize()
    {
        return serialize(array(
            $this->from,
            $this->to,
            $this->id,
            $this->message
        ));
    }


    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->from,
            $this->to,
            $this->id,
            $this->message
            ) = $data;
    }





}