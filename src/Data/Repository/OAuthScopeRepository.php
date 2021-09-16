<?php

declare(strict_types=1);

namespace Data\Repository;

use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server as OAuth;

class OAuthScopeRepository extends EntityRepository implements OAuth\Repositories\ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier): ?OAuth\Entities\ScopeEntityInterface
    {
        return $this->findOneBy(['scope' => $identifier]);
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
