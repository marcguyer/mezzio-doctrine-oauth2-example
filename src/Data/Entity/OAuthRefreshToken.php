<?php

declare(strict_types=1);

namespace Data\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities as OAuth;
use DateTimeImmutable;

#[ORM\Table(name: "oauth_refresh_tokens")]
#[ORM\Entity]
class OAuthRefreshToken implements OAuth\RefreshTokenEntityInterface
{
    #[ORM\Column(name: "id", type: "integer", options: ["unsigned" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "OAuthAccessToken")]
    #[ORM\JoinColumn(name: "access_token_id", referencedColumnName: "id")]
    private OAuthAccessToken $accessToken;

    #[ORM\Column(type: "boolean", options: ["default" => 0])]
    private bool $isRevoked = false;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $expiresDatetime;

    public function __construct()
    {
        $this->id = 0;
        $this->expiresDatetime = new DateTimeImmutable();
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

    public function getIdentifier(): string
    {
        return (string)$this->getId();
    }

    /**
     * @return static
     */
    public function setIdentifier(mixed $identifier)
    {
        $this->setId((int)$identifier);

        return $this;
    }

    public function setAccessToken(OAuth\AccessTokenEntityInterface $accessToken): self
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getAccessToken(): OAuthAccessToken
    {
        return $this->accessToken;
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
