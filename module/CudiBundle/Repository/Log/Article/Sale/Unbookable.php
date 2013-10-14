<?php

namespace CudiBundle\Repository\Log\Article\Sale;

use DateTime,
    CommonBundle\Component\Util\EntityRepository;

/**
 * Unbookable
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Unbookable extends EntityRepository
{
    public function findAllAfter(DateTime $date)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('l')
            ->from('CudiBundle\Entity\Log\Article\Sale\Unbookable', 'l')
            ->where(
                $query->expr()->gt('l.timestamp', ':date')
            )
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
