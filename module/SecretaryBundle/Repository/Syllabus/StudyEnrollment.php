<?php

namespace SecretaryBundle\Repository\Syllabus;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Component\Util\EntityRepository,
    SyllabusBUndle\Entity\Study;

/**
 * StudyEnrollment
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudyEnrollment extends EntityRepository
{
    public function findAllByStudyAndAcademicYearQuery(Study $study, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SecretaryBundle\Entity\Syllabus\StudyEnrollment', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.study', ':study'),
                    $query->expr()->eq('s.academicYear', ':academicYear')
                )
            )
            ->setParameter('study', $study)
            ->setParameter('academicYear', $academicYear)
            ->getQuery();

        return $resultSet;
    }

    public function findAllByAcademicAndAcademicYearQuery(Academic $academic, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SecretaryBundle\Entity\Syllabus\StudyEnrollment', 's')
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

    public function findOneByAcademicAndAcademicYearAndStudy(Academic $academic, AcademicYear $academicYear, Study $study)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SecretaryBundle\Entity\Syllabus\StudyEnrollment', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.academic', ':academic'),
                    $query->expr()->eq('s.academicYear', ':academicYear'),
                    $query->expr()->eq('s.study', ':study')
                )
            )
            ->setParameter('academic', $academic)
            ->setParameter('academicYear', $academicYear)
            ->setParameter('study', $study)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
