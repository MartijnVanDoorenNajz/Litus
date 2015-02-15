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
 *
 * @license http://litus.cc/LICENSE
 */

namespace PromBundle\Repository\Bus;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Passenger
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Passenger extends EntityRepository
{
    public function findPassengerByCodeQuery($code)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PromBundle\Entity\Bus\Passenger', 'p')
            ->where(
                    $query->expr()->eq('p.code', ':code')
            )
            ->setParameter('code', $code)
            ->getQuery();

        return $resultSet;
    }

    public function findPassengerByEmailQuery($email)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PromBundle\Entity\Bus\Passenger', 'p')
            ->where(
                    $query->expr()->eq('p.email', ':email')
            )
            ->setParameter('code', $email)
            ->getQuery();

        return $resultSet;
    }
}
