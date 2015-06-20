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
 * ReservationCode
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReservationCode extends EntityRepository
{
    public function codeExist($code)
    {
        $query = $this->_em->createQueryBuilder();
        $resultCode = $query->select('c')
            ->from('PromBundle\Entity\Bus\ReservationCode', 'c')
            ->where(
                $query->expr()->eq('c.code', ':code')
            )
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult();

        return (empty($resultCode) ? false : true);
    }

    public function getRegistrationCodeByCode($code)
    {
        $query = $this->_em->createQueryBuilder();
        $resultCode = $query->select('c')
            ->from('PromBundle\Entity\Bus\ReservationCode', 'c')
            ->where(
                $query->expr()->eq('c.code', ':code')
            )
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult();

        return $resultCode[0];
    }
}