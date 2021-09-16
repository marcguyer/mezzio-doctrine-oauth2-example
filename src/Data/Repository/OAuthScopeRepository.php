<?php

declare(strict_types=1);

namespace Data\Repository;

use Data\Entity\OAuthScope;
use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server as OAuth;

class OAuthScopeRepository extends EntityRepository implements OAuth\Repositories\ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier): ?OAuth\Entities\ScopeEntityInterface
    {
        /** @var ?OAuthScope $scope */
        $scope = $this->findOneBy(['scope' => $identifier]);

        return $scope;
    }

    public function finalizeScopes(
        array $scopes,
        $grantType,
        OAuth\Entities\ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array {
        return $scopes;
    }
}
