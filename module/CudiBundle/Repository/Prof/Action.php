<?php

namespace CudiBundle\Repository\Prof;

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityRepository;

/**
 * Action
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Action extends EntityRepository
{
    public function findAllUncompleted($nbResults = null)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->isNull('a.refuseDate'),
                    $query->expr()->isNull('a.confirmDate')
                )
            )
            ->orderBy('a.timestamp', 'ASC')
            ->setMaxResults($nbResults)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllCompleted()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                   $query->expr()->isNotNull('a.confirmDate')
            )
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllRefused()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                   $query->expr()->isNotNull('a.refuseDate')
            )
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByPerson(Person $person)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->eq('a.person', ':person')
            )
            ->setParameter('person', $person->getId())
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByEntityAndActionAndPerson($entity, $action, Person $person)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.person', ':person'),
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.action', ':action')
                )
            )
            ->setParameter('person', $person->getId())
            ->setParameter('entity', $entity)
            ->setParameter('action', $action)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByEntityAndEntityIdAndActionAndPerson($entity, $entityId, $action, Person $person)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.person', ':person'),
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.entityId', ':entityId'),
                    $query->expr()->eq('a.action', ':action')
                )
            )
            ->setParameter('person', $person->getId())
            ->setParameter('entity', $entity)
            ->setParameter('entityId', $entityId)
            ->setParameter('action', $action)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByEntityAndEntityIdAndPerson($entity, $entityId, Person $person)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.person', ':person'),
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.entityId', ':entityId')
                )
            )
            ->setParameter('person', $person->getId())
            ->setParameter('entity', $entity)
            ->setParameter('entityId', $entityId)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByEntityAndPreviousIdAndAction($entity, $previousId, $action)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.previousId', ':previousId'),
                    $query->expr()->eq('a.action', ':action')
                )
            )
            ->setParameter('entity', $entity)
            ->setParameter('previousId', $previousId)
            ->setParameter('action', $action)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByEntityAndEntityIdAndAction($entity, $entityId, $action)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.entityId', ':entityId'),
                    $query->expr()->eq('a.action', ':action')
                )
            )
            ->setParameter('entity', $entity)
            ->setParameter('entityId', $entityId)
            ->setParameter('action', $action)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
