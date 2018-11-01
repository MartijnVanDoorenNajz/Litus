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

namespace SecretaryBundle\Repository\Syllabus;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use SyllabusBundle\Entity\Study;

/**
 * StudyEnrollment
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudyEnrollment extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllByStudyQuery(Study $study)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('SecretaryBundle\Entity\Syllabus\StudyEnrollment', 's')
            ->where(
                $query->expr()->eq('s.study', ':study')
            )
            ->setParameter('study', $study)
            ->getQuery();
    }

    public function findAllByAcademicAndAcademicYearQuery(Academic $academic, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
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
    }

    public function findOneByAcademicAndStudy(Academic $academic, Study $study)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('SecretaryBundle\Entity\Syllabus\StudyEnrollment', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.academic', ':academic'),
                    $query->expr()->eq('s.study', ':study')
                )
            )
            ->setParameter('academic', $academic)
            ->setParameter('study', $study)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
