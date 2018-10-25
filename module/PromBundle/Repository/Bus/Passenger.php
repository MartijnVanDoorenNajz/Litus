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

namespace PromBundle\Repository\Bus;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;
use CommonBundle\Entity\General\AcademicYear;
use PromBundle\Entity\Bus;

/**
 * Passenger
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Passenger extends EntityRepository
{
    /**
     * @param  string              $code
     * @return \Doctrine\ORM\Query
     */
    public function findPassengerByCodeQuery($code)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PromBundle\Entity\Bus\Passenger', 'p')
            ->where(
                $query->expr()->eq('p.code', ':code')
            )
            ->setParameter('code', $code)
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  string              $email
     * @param                      $academicYear AcademicYear
     * @return \Doctrine\ORM\Query
     */
    public function findPassengerByEmail($email, $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PromBundle\Entity\Bus\Passenger', 'p')
            ->join('p.code', 'c')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.email', ':email'),
                    $query->expr()->eq('c.academicYear', ':year')
                )
            )
            ->setParameter('year', $academicYear)
            ->setParameter('email', $email)
        ->getQuery()
        ->getOneOrNullResult();

        return $resultSet;
    }

    /**
     * @param  Bus                 $bus
     * @return \Doctrine\ORM\Query
     */
    public function findAllPassengersByBusQuery(Bus $bus)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PromBundle\Entity\Bus\Passenger', 'p')
            ->where(
                $query->expr()->eq('p.firstBus', ':bus')
            )
            ->setParameter('bus', $bus)
            ->orderBy('p.firstName', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  AcademicYear        $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllPassengersByAcademicYearFirstBus(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PromBundle\Entity\Bus\Passenger', 'p')
            ->innerJoin('p.firstBus', 'b')
            ->where(
                $query->expr()->eq('b.academicYear', ':academicYear')
            )
            ->setParameter('academicYear', $academicYear)
            ->orderBy('p.firstName', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
