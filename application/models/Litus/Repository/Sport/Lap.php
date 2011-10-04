<?php

namespace Litus\Repository\Sport;

use Litus\Application\Resource\Doctrine;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use Zend\Registry;

/**
 * Lap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Lap extends EntityRepository
{
    public function findPrevious($nbResults = 1)
    {
        $queryBuilder = new QueryBuilder(
            Registry::get(Doctrine::REGISTRY_KEY)
        );

        $queryBuilder->select('l')
            ->from('Litus\Entity\Sport\Lap', 'l')
            ->where('l.startTime is not null')
            ->orderBy('l.registrationTime', 'DESC')
            ->setMaxResults($nbResults + 1);
        $resultSet = $queryBuilder->getQuery()
            ->getResult();

        unset($resultSet[0]);

        if (1 == $nbResults)
            return $resultSet[0];

        return array_reverse($resultSet);
    }

    public function findCurrent()
    {
        $queryBuilder = new QueryBuilder(
            Registry::get(Doctrine::REGISTRY_KEY)
        );

        $queryBuilder->select('l')
            ->from('Litus\Entity\Sport\Lap', 'l')
            ->where('l.startTime is not null')
            ->orderBy('l.registrationTime', 'DESC')
            ->setMaxResults(1);
        $resultSet = $queryBuilder->getQuery()
            ->getResult();

        return $resultSet[0];
    }

    public function findNext($nbResults = 1)
    {
        $queryBuilder = new QueryBuilder(
            Registry::get(Doctrine::REGISTRY_KEY)
        );

        $queryBuilder->select('l')
            ->from('Litus\Entity\Sport\Lap', 'l')
            ->where('l.startTime is null')
            ->orderBy('l.registrationTime', 'ASC')
            ->setMaxResults($nbResults);
        $resultSet = $queryBuilder->getQuery()
            ->getResult();

        if (1 == $nbResults)
            return isset($resultSet[0]) ? $resultSet[0] : null;

        return $resultSet;
    }

    public function countAll()
    {
        $queryBuilder = new QueryBuilder(
            Registry::get(Doctrine::REGISTRY_KEY)
        );

        $queryBuilder->select('l')
            ->from('Litus\Entity\Sport\Lap', 'l')
            ->select($queryBuilder->expr()->count('l.id'))
            ->where('l.startTime is not null');

        $resultSet = $queryBuilder->getQuery()
            ->getResult();

        return $resultSet[0][1];
    }
}