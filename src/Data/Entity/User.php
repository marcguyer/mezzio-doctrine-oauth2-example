<?php
declare(strict_types=1);

namespace Data\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mezzio\Authentication\UserInterface as MezzioUserInterface;
use League\OAuth2\Server\Entities\UserEntityInterface as OAuth2UserInterface;

#[ORM\Table(name: "users")]
#[ORM\Entity]
class User implements MezzioUserInterface, OAuth2UserInterface
{
    #[ORM\Column(name: "id", type: "integer", options: ["unsigned" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(name: "active", type: "boolean", options: ["default" => 0])]
    private bool $isActive = false;

    private array $roles = [];

    private array $details = [];

    public function __construct()
    {
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setIsActive(bool $isActive = true): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getIdentity(): string
    {
        return $this->getEmail();
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getDetail(string $name, $default = null)
    {
        return $this->details[$name] ?? $default;
    }

    public function setDetails(array $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}