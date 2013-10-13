<?php

namespace LogisticsBundle\Repository\Reservation;

use DateTime,
    CommonBundle\Component\Util\EntityRepository;

/**
 * VanReservation
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VanReservation extends EntityRepository
{
    public function findAllActive()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\VanReservation', 'r')
            ->where(
                $query->expr()->gte('r.endDate', ':start')
            )
            ->setParameter('start', new DateTime())
            ->orderBy('r.startDate')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllOld()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\VanReservation', 'r')
            ->where(
                $query->expr()->lt('r.endDate', ':end')
            )
            ->setParameter('end', new DateTime())
            ->orderBy('r.startDate')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByDates(DateTime $start, DateTime $end)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\VanReservation', 'r')
            ->where(
                $query->expr()->orx(
                    $query->expr()->andx(
                        $query->expr()->gte('r.startDate', ':start'),
                        $query->expr()->lte('r.startDate', ':end')
                    ),
                    $query->expr()->andx(
                        $query->expr()->gte('r.endDate', ':start'),
                        $query->expr()->lte('r.endDate', ':end')
                    )
                )
            )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
