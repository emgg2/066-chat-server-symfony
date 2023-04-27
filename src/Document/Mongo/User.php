<?php
namespace App\Document\Mongo;


use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(collection="User", repositoryClass="App\Repository\Mongo\UserRepository")
 * @MongoDB\HasLifecycleCallbacks
 */

// If you want the User object to be serialized to the session, you need to implement Serializable
// https://symfony.com/doc/current/security/entity_provider.html#what-do-the-serialize-and-unserialize-methods-do
class User extends AbstractController implements UserInterface
{

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @MongoDB\Field(type="boolean")
     * @Assert\NotBlank()
     */
    protected $online;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $password;



    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->online = false;
    }

    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }


    /**
     * Get id
     *
     * @return $id
     */
    public function getUserIdentifier()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getUsername()
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
     * Get Online
     *
     * @return bool $online
     */
    public function getOnline()
    {
        return $this->online;
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