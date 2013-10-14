<?php

namespace SecretaryBundle\Repository\Syllabus;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Component\Util\EntityRepository,
    SyllabusBundle\Entity\Subject;

/**
 * SubjectEnrollment
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubjectEnrollment extends EntityRepository
{
    public function findAllByAcademicAndAcademicYearQuery(Academic $academic, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SecretaryBundle\Entity\Syllabus\SubjectEnrollment', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.academic', ':academic'),
                    $query->expr()->eq('s.academicYear', ':academicYear')
                )
            )
            ->setParameter('academic', $academic)
            ->setParameter('academicYear', $academicYear)
            ->getQuery();

        return $resultSet;
    }

    public function findOneByAcademicAndAcademicYearAndSubject(Academic $academic, AcademicYear $academicYear, Subject $subject)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SecretaryBundle\Entity\Syllabus\SubjectEnrollment', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.academic', ':academic'),
                    $query->expr()->eq('s.academicYear', ':academicYear'),
                    $query->expr()->eq('s.subject', ':subject')
                )
            )
            ->setParameter('academic', $academic)
            ->setParameter('academicYear', $academicYear)
            ->setParameter('subject', $subject)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
