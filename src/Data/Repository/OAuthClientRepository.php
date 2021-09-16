<?php

declare(strict_types=1);

namespace Data\Repository;

use Data\Entity\OAuthClient;
use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server as OAuth;

class OAuthClientRepository extends EntityRepository implements OAuth\Repositories\ClientRepositoryInterface
{
    public function getClientEntity($clientIdentifier): ?OAuth\Entities\ClientEntityInterface
    {
        /** @var ?OAuthClient */
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

        /** @psalm-suppress UndefinedInterfaceMethod */
        if (empty($client->getSecret())) {
            return false;
        }

        /**
         * @psalm-suppress UndefinedInterfaceMethod
         * @psalm-suppress MixedArgument
         */
        return password_verify((string)$clientSecret, $client->getSecret());
    }

    /** @psalm-suppress UnusedParam */
    private function isGranted(OAuth\Entities\ClientEntityInterface $client, string $grantType = null): bool
    {
        return match ($grantType) {
            null, 'client_credentials', 'authorization_code', 'refresh_token' => true,
            default => false,
        };
    }

}
