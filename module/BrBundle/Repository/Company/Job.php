<?php

namespace BrBundle\Repository\Company;

use BrBundle\Entity\Company as CompanyEntity,
    DateTime,
    CommonBundle\Component\Util\EntityRepository;

/**
 * Job
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Job extends EntityRepository
{
    public function findOneActiveByTypeAndId($type, $id)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('v')
            ->from('BrBundle\Entity\Company\Job', 'v')
            ->innerJoin('v.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('v.type', ':type'),
                    $query->expr()->eq('v.id', ':id'),
                    $query->expr()->lt('v.startDate', ':now'),
                    $query->expr()->gt('v.endDate', ':now'),
                    $query->expr()->eq('c.active', 'true')
                )
            )
            ->setParameter('id', $id)
            ->setParameter('type', $type)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findAllByCompany(CompanyEntity $company)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('v')
            ->from('BrBundle\Entity\Company\Job', 'v')
            ->where(
                $query->expr()->eq('v.company', ':company')
            )
            ->setParameter('company', $company->getId())
            ->orderBy('v.type', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllActiveByType($type)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('v')
            ->from('BrBundle\Entity\Company\Job', 'v')
            ->innerJoin('v.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('v.type', ':type'),
                    $query->expr()->lt('v.startDate', ':now'),
                    $query->expr()->gt('v.endDate', ':now'),
                    $query->expr()->eq('c.active', 'true')
                )
            )
            ->setParameter('type', $type)
            ->setParameter('now', new DateTime())
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('v.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllActiveByCompanyAndType(CompanyEntity $company, $type)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('v')
            ->from('BrBundle\Entity\Company\Job', 'v')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('v.company', ':company'),
                    $query->expr()->eq('v.type', ':type'),
                    $query->expr()->lt('v.startDate', ':now'),
                    $query->expr()->gt('v.endDate', ':now')
                )
            )
            ->setParameter('type', $type)
            ->setParameter('company', $company->getId())
            ->setParameter('now', new DateTime())
            ->orderBy('v.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
