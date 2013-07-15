<?php

namespace CommonBundle\Repository\User\Person\Organization;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    Doctrine\ORM\EntityRepository;

/**
 * AcademicYearMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AcademicYearMap extends EntityRepository
{
    public function findOneByAcademicAndAcademicYear(Academic $academic, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.academic', ':academic'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academic', $academic->getId())
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}