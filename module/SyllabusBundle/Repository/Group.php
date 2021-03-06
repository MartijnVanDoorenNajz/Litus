<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Repository;

use CommonBundle\Entity\General\AcademicYear;
use SyllabusBundle\Entity\Group as GroupEntity;

/**
 * Group
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Group extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('g')
            ->from('SyllabusBundle\Entity\Group', 'g')
            ->where(
                $query->expr()->eq('g.removed', 'false')
            )
            ->getQuery();
    }

    /**
     * @param  GroupEntity  $group
     * @param  AcademicYear $academicYear
     * @return integer
     */
    public function findNbStudentsByGroupAndAcademicYear(GroupEntity $group, AcademicYear $academicYear)
    {
        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group\StudyMap')
            ->findAllByGroupAndAcademicYear($group, $academicYear);

        $ids = array(0);
        foreach ($studies as $study) {
            $ids[] = $study->getStudy()->getId();
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select($query->expr()->count('e'))
            ->from('SecretaryBundle\Entity\Syllabus\Enrollment\Study', 'e')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('e.study', $ids),
                    $query->expr()->eq('e.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getSingleScalarResult();

        if ($resultSet !== null) {
            return $resultSet;
        }

        return 0;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllCvBookQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('g')
            ->from('SyllabusBundle\Entity\Group', 'g')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('g.cvBook', 'true'),
                    $query->expr()->eq('g.removed', 'false')
                )
            )
            ->orderBy('g.name', 'ASC')
            ->getQuery();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllPocGroupsByAcademicYear(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('g')
            ->from('SyllabusBundle\Entity\Group', 'g')
            ->where(
                $query->expr()->eq('p.academicYear', ':academicYear'),
                $query->expr()->eq('p.indicator', 'true')
            )
            ->setParameter('academicYear', $academicYear)
            ->innerJoin('p.groupId', 'g')
            ->orderBy('g.name', 'ASC')
            ->getQuery();
    }
}
