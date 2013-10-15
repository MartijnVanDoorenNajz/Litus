<?php

namespace TicketBundle\Repository;

use CommonBundle\Entity\User\Person,
    CommonBundle\Component\Doctrine\ORM\EntityRepository,
    TicketBundle\Entity\Event as EventEntity;

/**
 * Ticket
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Ticket extends EntityRepository
{
    public function findOneByEventAndNumber(EventEntity $event, $number)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.event', ':event'),
                    $query->expr()->eq('t.number', ':number')
                )
            )
            ->setParameter('event', $event)
            ->setParameter('number', $number)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findAllByEventQuery(EventEntity $event)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->where(
                $query->expr()->eq('t.event', ':event')
            )
            ->setParameter('event', $event)
            ->getQuery();

        return $resultSet;
    }

    public function findAllByEventAndPersonQuery(EventEntity $event, Person $person)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.person', ':person'),
                    $query->expr()->eq('t.event', ':event')
                )
            )
            ->setParameter('person', $person)
            ->setParameter('event', $event)
            ->getQuery();

        return $resultSet;
    }

    public function findAllEmptyByEventQuery(EventEntity $event)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.event', ':event'),
                    $query->expr()->eq('t.status', ':empty')
                )
            )
            ->setParameter('event', $event)
            ->setParameter('empty', 'empty')
            ->getQuery();

        return $resultSet;
    }
}
