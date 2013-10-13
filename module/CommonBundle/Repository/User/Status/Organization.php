<?php

namespace CommonBundle\Repository\User\Status;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Component\Util\EntityRepository;

/**
 * Organization
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Organization extends EntityRepository
{
    public function findAllByStatus($status, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('CommonBundle\Entity\User\Status\Organization', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.status', ':status'),
                    $query->expr()->eq('s.academicYear', ':academicYear')
                )
            )
            ->setParameter('status', $status)
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
