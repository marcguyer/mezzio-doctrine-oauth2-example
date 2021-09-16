<?php
declare(strict_types=1);

namespace Data\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities as OAuth;

#[ORM\Table(name: "oauth_scopes")]
#[ORM\Entity]
class OAuthScope implements OAuth\ScopeEntityInterface
{
    use OAuth\Traits\ScopeTrait;

    #[ORM\Column(name: "id", type: "integer", options: ["unsigned" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $scope;

    #[ORM\ManyToMany(targetEntity: OAuthAccessToken::class, mappedBy: "scopes")]
    protected Collection $accessTokens;

    #[ORM\ManyToMany(targetEntity: OAuthAuthCode::class, mappedBy: "scopes")]
    protected Collection $authCodes;

    public function __construct()
    {
        $this->id = 0;
        $this->scope = '';
        $this->accessTokens = new ArrayCollection();
        $this->authCodes = new ArrayCollection();
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
        return $this->getScope();
    }

    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function addAccessToken(OAuthAccessToken $accessToken): self
    {
        if ($this->accessTokens->contains($accessToken)) {
            return $this;
        }

        $this->accessTokens->add($accessToken);

        return $this;
    }

    public function removeAccessToken(OAuthAccessToken $accessToken): self
    {
        $this->accessTokens->removeElement($accessToken);

        return $this;
    }

    /** @psalm-suppress MixedInferredReturnType */
    public function getAccessTokens(?Criteria $criteria = null): Collection
    {
        if ($criteria === null) {
            return $this->accessTokens;
        }

        /**
         * @psalm-suppress UndefinedInterfaceMethod
         * @psalm-suppress MixedReturnStatement
         */
        return $this->accessTokens->matching($criteria);
    }

    public function addAuthCode(OAuthAuthCode $authCode): self
    {
        if ($this->authCodes->contains($authCode)) {
            return $this;
        }

        $this->authCodes->add($authCode);

        return $this;
    }

    public function removeAuthCode(OAuthAuthCode $authCode): self
    {
        $this->authCodes->removeElement($authCode);

        /** @psalm-suppress MixedInferredReturnType */
        return $this;
    }

    /** @psalm-suppress MixedInferredReturnType */
    public function getAuthCodes(?Criteria $criteria = null): Collection
    {
        if ($criteria === null) {
            /** @psalm-suppress MixedInferredReturnType */
            return $this->authCodes;
        }

        /**
         * @psalm-suppress UndefinedInterfaceMethod
         * @psalm-suppress MixedReturnStatement
         */
        return $this->authCodes->matching($criteria);
    }
}
