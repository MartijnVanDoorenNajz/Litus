<?php

namespace CommonBundle\Repository\General\Bank;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * MoneyUnit
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MoneyUnit extends EntityRepository
{
    public function findOneByUnit($unit)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('u')
            ->from('CommonBundle\Entity\General\Bank\MoneyUnit', 'u')
            ->where(
                $query->expr()->eq('u.unit', ':unit')
            )
            ->setParameter('unit', $unit * 100)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }
}
