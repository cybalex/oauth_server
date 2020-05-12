<?php

namespace Cybalex\OauthServer\Entity\ORM;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $username;

    /**
     * @ORM\Column(name="username_canonical", type="string", length=180, unique=true)
     */
    private $usernameCanonical;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $email;

    /**
     * @ORM\Column(name="email_canonical", type="string", length=180, unique=true)
     */
    private $emailCanonical;

    /**
     * @ORM\Column(type="string")
     */
    private $salt;

    /**
     * @var string|null
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var DateTime|null
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles = [];

    public function __construct()
    {
        $this->enabled = false;
        $this->salt = md5(uniqid(null, true));
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $usernameCanonical
     * @return $this
     */
    public function setUsernameCanonical(string $usernameCanonical): self
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsernameCanonical(): string
    {
        return $this->usernameCanonical;
    }

    /**
     * @param string|null $salt
     * @return $this
     */
    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string|null $plainPassword
     * @return $this
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return $this
     */
    public function eraseCredentials(): self
    {
        $this->plainPassword = null;

        return $this;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $emailCanonical
     * @return $this
     */
    public function setEmailCanonical(string $emailCanonical): self
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailCanonical(): string
    {
        return $this->emailCanonical;
    }

    /**
     * @param $enabled
     * @return $this
     */
    public function setEnabled($enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param DateTime|null $time
     * @return $this
     */
    public function setLastLogin(DateTime $time = null): self
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * Gets the last login time.
     */
    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }
}
