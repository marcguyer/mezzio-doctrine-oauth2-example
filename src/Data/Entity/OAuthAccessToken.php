<?php
declare(strict_types=1);

namespace Data\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities as OAuth;

#[ORM\Table(name: "oauth_access_tokens")]
#[ORM\Entity]
class OAuthAccessToken implements OAuth\AccessTokenEntityInterface
{
    #[ORM\Column(name: "id", type: "integer", options: ["unsigned" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: OAuthClient::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id")]
    private OAuth\ClientEntityInterface $client;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", nullable: true)]
    private ?User $user;

    #[ORM\Column(length: 100)]
    private string $token;

    #[ORM\Column(type: "boolean", options: ["default" => 0])]
    private bool $isRevoked = false;

    #[ORM\ManyToMany(targetEntity: OAuthScope::class, inversedBy: "accessTokens", indexBy: "id")]
    #[ORM\JoinTable(name: "oauth_access_token_scopes")]
    #[ORM\JoinColumn(name: "access_token_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "scope_id", referencedColumnName: "id")]
    protected Collection $scopes;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $expiresDatetime;

    private ?CryptKey $privateKey = null;

    private ?Configuration $jwtConfiguration = null;

    public function __construct()
    {
        $this->id = 0;
        $this->expiresDatetime = new \DateTimeImmutable();
        $this->user = null;
        $this->token = '';
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

    public function setUser(?User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
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

    public function getIdentifier(): string
    {
        return $this->getToken();
    }

    public function setIdentifier(mixed $identifier): self
    {
        return $this->setToken($identifier);
    }

    public function setUserIdentifier($identifier): self
    {
        // not sure what this is for
        // just making the interface happy for now
        return $this;
    }

    public function getUserIdentifier()
    {
        if (null === $user = $this->getUser()) {
            return '';
        }

        return $user->getIdentifier();
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

    public function getScopes(?Criteria $criteria = null): Collection
    {
        if ($criteria === null) {
            return $this->scopes;
        }

        /** @psalm-suppress UndefinedInterfaceMethod */
        return $this->scopes->matching($criteria);
    }

    public function setExpiresDatetime(\DateTimeImmutable $expiresDatetime): self
    {
        $this->expiresDatetime = $expiresDatetime;

        return $this;
    }

    public function getExpiresDatetime(): \DateTimeImmutable
    {
        return $this->expiresDatetime;
    }

    public function getExpiryDateTime(): \DateTimeImmutable
    {
        return $this->getExpiresDatetime();
    }

    public function setExpiryDateTime(\DateTimeImmutable $dateTime): self
    {
        return $this->setExpiresDatetime($dateTime);
    }

    /** Set key used to encrypt token */
    public function setPrivateKey(CryptKey $privateKey): self
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /** Initialise the JWT Configuration. */
    public function initJwtConfiguration(): self
    {
        if (null === $this->privateKey) {
            throw new \RuntimeException('Unable to init JWT without private key');
        }
        $this->jwtConfiguration = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText(
                $this->privateKey->getKeyContents(),
                $this->privateKey->getPassPhrase() ?? ''
            ),
            InMemory::plainText('')
        );

        return $this;
    }

    /**
     * Generate a JWT from the access token
     */
    private function convertToJWT(): Token
    {
        $this->initJwtConfiguration();

        if (null === $this->jwtConfiguration) {
            throw new \RuntimeException('Unable to convert to JWT without config');
        }

        return $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new \DateTimeImmutable())
            ->canOnlyBeUsedAfter(new \DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes())
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }

    /**
     * Generate a string representation from the access token
     */
    public function __toString(): string
    {
        return $this->convertToJWT()->toString();
    }
}