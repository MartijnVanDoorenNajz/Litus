<?php

namespace SyllabusBundle\Repository;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Component\Doctrine\ORM\EntityRepository,
    SyllabusBundle\Entity\Subject as SubjectEntity,
    SyllabusBundle\Entity\Study as StudyEntity;

/**
 * StudySubjectMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudySubjectMap extends EntityRepository
{
    public function findAllByStudyAndAcademicYearQuery(StudyEntity $study, AcademicYear $academicYear)
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
            ->getQuery();

        return $resultSet;
    }

    public function findAllByNameAndStudyAndAcademicYearQuery($name, StudyEntity $study, AcademicYear $academicYear)
    {
        $parentIds = array($study->getId());
        foreach($study->getParents() as $parent) {
            $parentIds[] = $parent->getId();
        }

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                    $query->expr()->in('m.study', $parentIds),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('academicYear', $academicYear)
            ->getQuery();

        return $resultSet;
    }

    public function findAllByCodeAndStudyAndAcademicYearQuery($code, StudyEntity $study, AcademicYear $academicYear)
    {
        $parentIds = array($study->getId());
        foreach($study->getParents() as $parent) {
            $parentIds[] = $parent->getId();
        }

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.code'), ':code'),
                    $query->expr()->in('m.study', $parentIds),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('code', '%' . strtolower($code) . '%')
            ->setParameter('academicYear', $academicYear)
            ->getQuery();

        return $resultSet;
    }

    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s.id')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->eq('m.academicYear', ':academicYear')
            )
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        $ids = array(0 => 0);
        foreach($resultSet as $item)
            $ids[$item['id']] = $item['id'];

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->in('s.id', $ids)
            )
            ->orderBy('s.code', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByNameAndAcademicYearQuery($name, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s.id')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery()
            ->getResult();

        $ids = array(0 => 0);
        foreach($resultSet as $item)
            $ids[$item['id']] = $item['id'];

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->in('s.id', $ids)
            )
            ->orderBy('s.code', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByCodeAndAcademicYearQuery($code, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s.id')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.code'), ':code'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->setParameter('code', '%' . strtolower($code) . '%')
            ->getQuery()
            ->getResult();

        $ids = array(0 => 0);
        foreach($resultSet as $item)
            $ids[$item['id']] = $item['id'];

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->in('s.id', $ids)
            )
            ->orderBy('s.code', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllBySubjectAndAcademicYearQuery(SubjectEntity $subject, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.subject', ':subject'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->setParameter('subject', $subject)
            ->getQuery();

        return $resultSet;
    }

    public function findOneByStudySubjectAndAcademicYear(StudyEntity $study, SubjectEntity $subject, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.study', ':study'),
                    $query->expr()->eq('m.subject', ':subject'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->setParameter('study', $study)
            ->setParameter('subject', $subject)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }
}
