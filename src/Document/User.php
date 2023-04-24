<?php
namespace App\Document;


use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @MongoDB\Document()
 */

// If you want the User object to be serialized to the session, you need to implement Serializable
// https://symfony.com/doc/current/security/entity_provider.html#what-do-the-serialize-and-unserialize-methods-do
class User implements AdvancedUserInterface
{

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $from;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $to;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $message;




    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->online = false;
    }



    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get password
     *
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function isOnline()
    {
        return $this->online;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }


    /**
     * Set email
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return self
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return self
     */
    public function setOnline($online): self
    {
        $this->online = (bool) $online;

        return $this;
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
            $this->password,
            $this->online,
            $this->id,
            $this->email,
            $this->name,
        ));
    }


    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->password,
            $this->online,
            $this->id,
            $this->email,
            $this->name,
            ) = $data;
    }

    public function toJsonSerialize() {
        return serialize(array(
            $this->online,
            $this->id,
            $this->email,
            $this->name,
        ));
    }



}