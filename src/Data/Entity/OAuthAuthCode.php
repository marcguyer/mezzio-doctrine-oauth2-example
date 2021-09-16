<?php

declare(strict_types=1);

namespace Data\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities as OAuth;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: "oauth_auth_codes")]
#[ORM\Entity]
class OAuthAuthCode implements OAuth\AuthCodeEntityInterface
{
    use OAuth\Traits\AuthCodeTrait;

    #[ORM\Column(name: "id", type: "integer", options: ["unsigned" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: OAuthClient::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id")]
    private OAuth\ClientEntityInterface $client;

    #[ORM\Column(type: "boolean", options: ["default" => 0])]
    private bool $isRevoked = false;

    #[ORM\ManyToMany(targetEntity: OAuthScope::class, inversedBy: "authCodes", indexBy: "id")]
    #[ORM\JoinTable(name: "oauth_auth_code_scopes")]
    #[ORM\JoinColumn(name: "auth_code_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "scope_id", referencedColumnName: "id")]
    protected Collection $scopes;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private DateTimeImmutable $expiresDatetime;

    public function __construct()
    {
        $this->id = 0;
        $this->expiresDatetime = new DateTimeImmutable();
        $this->scopes = new ArrayCollection();
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

    public function setClient(OAuth\ClientEntityInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getClient(): OAuth\ClientEntityInterface
    {
        return $this->client;
    }

    public function getIdentifier(): ?int
    {
        return $this->getId();
    }

    public function setIdentifier(mixed $identifier): self
    {
        /** @psalm-suppress MixedArgument */
        $this->setId($identifier);

        return $this;
    }

    /**
     * @return static
     * @psalm-suppress MissingParamType
     */
    public function setUserIdentifier($identifier)
    {
        return $this;
    }

    public function getUserIdentifier()
    {
        /** @var OAuthClient $client */
        $client = $this->getClient();

        if (null === $user = $client->getUser()) {
            return null;
        }

        return $user->getIdentifier();
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

    public function addScope(OAuth\ScopeEntityInterface $scope): self
    {
        if ($this->scopes->contains($scope)) {
            return $this;
        }

        $this->scopes->add($scope);

        return $this;
    }

    public function removeScope(OAuthScope $scope): self
    {
        $this->scopes->removeElement($scope);

        return $this;
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function getScopes(?Criteria $criteria = null): array
    {
        if ($criteria === null) {
            /** @psalm-suppress MixedReturnStatement */
            return $this->scopes->toArray();
        }

        /**
         * @psalm-suppress UndefinedInterfaceMethod
         * @psalm-suppress MixedReturnStatement
         * @psalm-suppress MixedMethodCall
         */
        return $this->scopes->matching($criteria)->toArray();
    }

    public function setExpiresDatetime(DateTimeImmutable $expiresDatetime): self
    {
        $this->expiresDatetime = $expiresDatetime;

        return $this;
    }

    public function getExpiresDatetime(): DateTimeImmutable
    {
        return $this->expiresDatetime;
    }

    public function getExpiryDateTime(): DateTimeImmutable
    {
        return $this->getExpiresDatetime();
    }

    public function setExpiryDateTime(DateTimeImmutable $dateTime): self
    {
        return $this->setExpiresDatetime($dateTime);
    }
}

