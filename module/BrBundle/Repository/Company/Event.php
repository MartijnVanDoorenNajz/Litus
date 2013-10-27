<?php

namespace BrBundle\Repository\Company;

use BrBundle\Entity\Company as CompanyEntity,
    \DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Event
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Event extends EntityRepository
{
    public function findAllByCompanyQuery(CompanyEntity $company)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'c')
            ->where(
                $query->expr()->eq('e.company', ':company')
            )
            ->setParameter('company', $company->getId())
            ->orderBy('c.startDate', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllFutureByCompanyQuery(DateTime $date, CompanyEntity $company)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->orx(
                        $query->expr()->gte('c.endDate', ':date'),
                        $query->expr()->gte('c.startDate', ':date')
                    ),
                    $query->expr()->eq('e.company', ':company')
                )
            )
            ->setParameter('company', $company->getId())
            ->setParameter('date', $date)
            ->orderBy('c.startDate', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllFutureQuery(DateTime $date)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'ev')
            ->innerJoin('e.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->orx(
                        $query->expr()->gte('ev.endDate', ':date'),
                        $query->expr()->gte('ev.startDate', ':date')
                    ),
                    $query->expr()->eq('c.active', 'true')
                )
            )
            ->setParameter('date', $date)
            ->orderBy('ev.startDate', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllFutureBySearchQuery(DateTime $date, $string)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'ev')
            ->innerJoin('e.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->orx(
                        $query->expr()->gte('ev.endDate', ':date'),
                        $query->expr()->gte('ev.startDate', ':date')
                    ),
                    $query->expr()->eq('c.active', 'true'),
                    $query->expr()->like('LOWER(c.name)', ':name')
                )
            )
            ->setParameter('date', $date)
            ->setParameter('name', strtolower($string))
            ->orderBy('ev.startDate', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findOneActiveById($id)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('BrBundle\Entity\Company\Event', 'e')
            ->innerJoin('e.event', 'ev')
            ->innerJoin('e.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->orx(
                        $query->expr()->gte('ev.endDate', ':date'),
                        $query->expr()->gte('ev.startDate', ':date')
                    ),
                    $query->expr()->eq('e.id', ':id'),
                    $query->expr()->eq('c.active', 'true')
                )
            )
            ->setParameter('date', new DateTime())
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }
}
