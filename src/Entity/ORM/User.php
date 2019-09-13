<?php

namespace Cybalex\OauthServer\Entity\ORM;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @var @ORM\Column(name="plain_password", type="string", length=255, nullable=true)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    public function setSalt(?string $salt): User
    {
        $this->salt = $salt;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setPassword($password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): User
    {
        $this->plainPassword = null;

        return $this;
    }

    public function setEmail(?string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->password,
                $this->salt,
                $this->username,
                $this->email,
            ]
        );
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->id,
            $this->password,
            $this->salt,
            $this->username,
            $this->email
        ) = $data;
    }
}
