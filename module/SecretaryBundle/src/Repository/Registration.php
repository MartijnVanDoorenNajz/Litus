<?php

namespace SecretaryBundle\Repository;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\People\Academic,
    Doctrine\ORM\EntityRepository;

/**
 * Registration
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Registration extends EntityRepository
{
    public function findOneByAcademicAndAcademicYear(Academic $academic, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.academic', ':academic'),
                    $query->expr()->eq('r.academicYear', ':academicYear')
                )
            )
            ->setParameter('academic', $academic)
            ->setParameter('academicYear', $academicYear)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
