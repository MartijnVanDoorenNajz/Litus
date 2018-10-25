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
 * Group
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Group extends EntityRepository
{
    /**
     * Returns the (count % 6) last groups entered
     *
     * @return array
     */
    public function findLast()
    {
        $builder = $this->getEntityManager()->createQueryBuilder();

        $count = $builder->select($builder->expr()->count('g.id'))
            ->from('SportBundle\Entity\Group', 'g')
            ->getQuery()
            ->getSingleScalarResult();

        $count = ($count % 6);

        // avoid second query if not needed
        if (0 === $count) {
            return array();
        }

        $builder = $this->getEntityManager()->createQueryBuilder();

        return $builder->select('g')
            ->from('SportBundle\Entity\Group', 'g')
            ->orderBy('g.id', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  AcademicYear        $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('g')
            ->from('SportBundle\Entity\Group', 'g')
            ->where(
                $query->expr()->eq('g.academicYear', ':academicYear')
            )
            ->setParameter('academicYear', $academicYear)
            ->getQuery();

        return $resultSet;
    }
}
