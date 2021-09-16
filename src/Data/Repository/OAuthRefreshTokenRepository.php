<?php
declare(strict_types=1);

namespace Data\Repository;

use Data\Entity\OAuthRefreshToken;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use League\OAuth2\Server as OAuth;

class OAuthRefreshTokenRepository extends EntityRepository implements OAuth\Repositories\RefreshTokenRepositoryInterface
{
    public function getNewRefreshToken(): OAuthRefreshToken
    {
        return new OAuthRefreshToken();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function persistNewRefreshToken(OAuth\Entities\RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $em = $this->getEntityManager();
        $em->persist($refreshTokenEntity);
        $em->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function revokeRefreshToken($tokenId): void
    {
        if (null === $refreshTokenEntity = $this->find($tokenId)) {
            return;
        }

        $refreshTokenEntity->setIsRevoked(true);
        $em = $this->getEntityManager();
        $em->persist($refreshTokenEntity);
        $em->flush();
    }

    public function isRefreshTokenRevoked($tokenId): bool
    {
        if (null === $refreshTokenEntity = $this->find($tokenId)) {
            return true;
        }

        return $refreshTokenEntity->getIsRevoked();
    }
}
