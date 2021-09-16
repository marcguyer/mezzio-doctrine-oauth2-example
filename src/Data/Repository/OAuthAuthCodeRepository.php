<?php
declare(strict_types=1);

namespace Data\Repository;

use Data\Entity\OAuthAuthCode;
use Doctrine\ORM;
use League\OAuth2\Server as OAuth;

class OAuthAuthCodeRepository extends ORM\EntityRepository implements OAuth\Repositories\AuthCodeRepositoryInterface
{
    public function getNewAuthCode(): OAuthAuthCode
    {
        return new OAuthAuthCode();
    }

    /**
     * @throws ORM\OptimisticLockException
     * @throws ORM\ORMException
     *
     * @return void
     */
    public function persistNewAuthCode(OAuth\Entities\AuthCodeEntityInterface $authCodeEntity)
    {
        $this->getEntityManager()->persist($authCodeEntity);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws ORM\OptimisticLockException
     * @throws ORM\ORMException
     *
     * @return void
     *
     * @param string $codeId
     */
    public function revokeAuthCode($codeId)
    {
        /** @var ?OAuthAuthCode $authCodeEntity */
        $authCodeEntity = $this->find($codeId);
        if (null === $authCodeEntity) {
            return;
        }
        $authCodeEntity->setIsRevoked(true);
        $this->getEntityManager()->persist($authCodeEntity);
        $this->getEntityManager()->flush();
    }

    public function isAuthCodeRevoked($codeId): bool
    {
        /** @var ?OAuthAuthCode $authCodeEntity */
        $authCodeEntity = $this->find($codeId);
        if (null === $authCodeEntity) {
            return true;
        }

        return $authCodeEntity->getIsRevoked();
    }
}
