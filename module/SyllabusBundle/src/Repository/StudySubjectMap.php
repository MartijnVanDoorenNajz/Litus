<?php

namespace SyllabusBundle\Repository;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join,
    SyllabusBundle\Entity\Study as StudyEntity;

/**
 * StudySubjectMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudySubjectMap extends EntityRepository
{
    public function findAllByStudyAndAcademicYear(StudyEntity $study, AcademicYear $academicYear)
    {
        $parentIds = array($study->getId());
        foreach($study->getParents() as $parent) {
            $parentIds[] = $parent->getId();
        }
        
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('m.study', $parentIds),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();
            
        return $resultSet;
    }
    
    public function findAllByNameAndStudyAndAcademicYear($name, StudyEntity $study, AcademicYear $academicYear)
    {
        $parentIds = array($study->getId());
        foreach($study->getParents() as $parent) {
            $parentIds[] = $parent->getId();
        }
        
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->innerJoin('m.subject', 's', Join::WITH, 
                $query->expr()->like($query->expr()->lower('s.name'), ':name')
            )
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('m.study', $parentIds),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();
            
        return $resultSet;
    }
    
    public function findAllByCodeAndStudy($code, StudyEntity $study, AcademicYear $academicYear)
    {
        $parentIds = array($study->getId());
        foreach($study->getParents() as $parent) {
            $parentIds[] = $parent->getId();
        }
        
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->innerJoin('m.subject', 's', Join::WITH, 
                $query->expr()->like($query->expr()->lower('s.code'), ':code')
            )
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('m.study', $parentIds),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('code', '%' . strtolower($code) . '%')
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();
            
        return $resultSet;
    }
}
