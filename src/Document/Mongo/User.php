<?php
namespace App\Document\Mongo;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;



/**
 * @MongoDB\Document(collection="User", repositoryClass="App\Repository\Mongo\UserRepository")
 * @MongoDB\HasLifecycleCallbacks
 */

// If you want the User object to be serialized to the session, you need to implement Serializable
// https://symfony.com/doc/current/security/entity_provider.html#what-do-the-serialize-and-unserialize-methods-do
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';

    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

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
     * @MongoDB\Field(type="collection")
     */
    protected $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->online = false;
        $this->roles = array();
    }

    /**
     *
     * @param string $role
     * @return $this
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
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
     *  get Name
     * @return mixed
     */

    public function getName()
    {
        return $this->name;
    }

    /**
     * Get username
     *
     * @return string $name
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Get password
     *
     * @return string $password
     */
    public function getPassword():string
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
     * Get roles
     *
     * @return array $roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }


    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @param $id
     */
    public function setId($id){
        $this->id = $id;
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
     * Set username
     *
     * @param string $username
     * @return self
     */
    public function setUsername($username): self
    {
        $this->username = $username;

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

    /**
     * Set roles
     *
     * @param array $roles
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

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


    public function isSuperAdmin()
    {
        return $this->hasRole('ROLE_SUPER_ADMIN');
    }

    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole('ROLE_SUPER_ADMIN');
        } else {
            $this->removeRole('ROLE_SUPER_ADMIN');
        }

        return $this;
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * @return array
     */
    public function getUserData(): array
    {
        return [
            'uid' => $this->getUserIdentifier(),
            'name' => $this->getName(),
            'online' => $this->getOnline(),
            'email' => $this->getUsername()
        ];
    }
}