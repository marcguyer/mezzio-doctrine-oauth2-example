<?php
declare(strict_types=1);

namespace Data\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ClientEntityInterface as OAuthClientInterface;
use Mezzio\Authentication\UserInterface as MezzioUserInterface;

#[ORM\Table(name: "oauth_clients")]
#[ORM\Entity]
class OAuthClient implements MezzioUserInterface, OAuthClientInterface
{
    #[ORM\Column(name: "id", type: "integer", options: ["unsigned" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", nullable: true)]
    private ?User $user = null;

    #[ORM\Column(length: 40)]
    private string $name;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $secret;

    #[ORM\Column(length: 255)]
    private string $redirect;

    #[ORM\Column(type: "boolean", options: ["default" => 0])]
    private bool $isRevoked = false;

    #[ORM\Column(type: "boolean", options: ["default" => 0])]
    private bool $isConfidential = false;

    private array $roles = [];
    private array $details = [];

    public function __construct()
    {
        $this->id = 0;
        $this->name = '';
        $this->secret = null;
        $this->redirect = '';
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(?User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getIdentity(): string
    {
        return $this->getName();
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

    public function setDetails(array $details): self
    {
        $this->details = $details;
        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function getDetail(string $name, $default = null)
    {
        return $this->details[$name] ?? $default;
    }

    public function getIdentifier()
    {
        return $this->getName();
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSecret(?string $secret = null): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setRedirect(string $redirect): self
    {
        $this->redirect = $redirect;

        return $this;
    }

    public function getRedirect(): string
    {
        return $this->redirect;
    }

    public function getRedirectUri(): ?string
    {
        return $this->getRedirect();
    }

    public function setIsRevoked(bool $isRevoked): self
    {
        $this->isRevoked = $isRevoked;

        return $this;
    }

    public function getIsRevoked(): bool
    {
        return $this->isRevoked;
    }

    public function setIsConfidential(bool $isConfidential): self
    {
        $this->isConfidential = $isConfidential;

        return $this;
    }

    public function getIsConfidential(): bool
    {
        return $this->isConfidential;
    }

    public function isConfidential(): bool
    {
        return $this->getIsConfidential();
    }
}
