<?php

namespace SyllabusBundle\Repository;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\People\Academic,
    Doctrine\ORM\EntityRepository,
    SyllabusBundle\Entity\Subject as SubjectEntity;
    
/**
 * SubjectProfMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubjectProfMap extends EntityRepository
{
    public function findOneBySubjectAndProfAndAcademicYear(SubjectEntity $subject, Academic $prof, AcademicYear $academicYear)
    {
        return $this->findOneBySubjectIdAndProfAndAcademicYear($subject->getId(), $prof, $academicYear);
    }
    
    public function findOneBySubjectIdAndProfAndAcademicYear($subjectId, Academic $prof, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\SubjectProfMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.subject', ':subject'),
                    $query->expr()->eq('m.prof', ':prof'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('subject', $subjectId)
            ->setParameter('prof', $prof->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        
        if (isset($resultSet[0]))
            return $resultSet[0];
        
        return null;
    }
    
    public function findAllBySubjectAndAcademicYear(SubjectEntity $subject, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\SubjectProfMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.subject', ':subject'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('subject', $subject->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery()
            ->getResult();
            
        return $resultSet;
    }
    
    public function findAllByProfAndAcademicYear(Academic $prof, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\SubjectProfMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.prof', ':prof'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('prof', $prof->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery()
            ->getResult();
            
        return $resultSet;
    }
    
    public function findAllByNameAndProfAndAcademicYearTypeAhead($name, Academic $prof, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\SubjectProfMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.prof', ':prof'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('prof', $prof->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery()
            ->getResult();
            
        return $resultSet;
    }
}
