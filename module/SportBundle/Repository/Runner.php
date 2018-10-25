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

namespace SportBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;
use CommonBundle\Entity\General\AcademicYear;

/**
 * Runner
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Runner extends EntityRepository
{
    public function findOneByUniversityIdentification($universityIdentification)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SportBundle\Entity\Runner', 'r')
            ->innerJoin('r.academic', 'a')
            ->where(
                $query->expr()->eq('a.universityIdentification', ':universityIdentification')
            )
            ->setParameter('universityIdentification', $universityIdentification)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findAllWithoutIdentificationQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SportBundle\Entity\Runner', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->isNull('r.runnerIdentification'),
                    $query->expr()->isNull('r.academic')
                )
            )
            ->getQuery();

        return $resultSet;
    }

    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('g')
            ->from('SportBundle\Entity\Runner', 'g')
            ->where(
                $query->expr()->eq('g.academicYear', ':academicYear')
            )
            ->setParameter('academicYear', $academicYear)
            ->getQuery();

        return $resultSet;
    }
}
