<?php

declare(strict_types=1);

namespace Data\Repository;

use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server as OAuth;

class OAuthClientRepository extends EntityRepository implements OAuth\Repositories\ClientRepositoryInterface
{
    public function getClientEntity($clientIdentifier): ?OAuth\Entities\ClientEntityInterface
    {
        return $this->findOneBy(['name' => $clientIdentifier]);
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $client = $this->getClientEntity($clientIdentifier);

        if (null === $client) {
            return false;
        }

        if (!$this->isGranted($client, $grantType)) {
            return false;
        }

        if (empty($client->getSecret())) {
            return false;
        }

        return password_verify((string)$clientSecret, $client->getSecret());
    }

    private function isGranted(OAuth\Entities\ClientEntityInterface $client, string $grantType = null): bool
    {
        return match ($grantType) {
            null, 'client_credentials', 'authorization_code', 'refresh_token' => true,
            default => false,
        };
    }

}
