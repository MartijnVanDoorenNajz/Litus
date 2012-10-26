<?php

namespace CalendarBundle\Repository\Nodes;

use DateTime,
    Doctrine\ORM\EntityRepository;

/**
 * Event
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Event extends EntityRepository
{
    public function findAllActive($nbResults = 15)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('e')
            ->from('CalendarBundle\Entity\Nodes\Event', 'e')
            ->where(
                $query->expr()->orX(
                    $query->expr()->gt('e.endDate', ':now'),
                    $query->expr()->gt('e.startDate', ':now')
                )
            )
            ->orderBy('e.startDate', 'ASC')
            ->setParameter('now', new DateTime());

        if ($nbResults > 0)
            $query->setMaxResults($nbResults);

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllOld()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('CalendarBundle\Entity\Nodes\Event', 'e')
            ->where(
                $query->expr()->lt('e.startDate', ':now')
            )
            ->orderBy('e.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllBetween(DateTime $first, DateTime $last)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('CalendarBundle\Entity\Nodes\Event', 'e')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gte('e.startDate', ':first'),
                    $query->expr()->lt('e.startDate', ':last')
                )
            )
            ->orderBy('e.startDate', 'ASC')
            ->setParameter('first', $first)
            ->setParameter('last', $last)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
