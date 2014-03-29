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

namespace BannerBundle\Repository\Node;

use DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Banner
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Banner extends EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('BannerBundle\Entity\Node\Banner', 'n')
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllActiveQuery()
    {
        $now = new DateTime();

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('BannerBundle\Entity\Node\Banner', 'n')
            ->where(
                $query->expr()->andx(
                    $query->expr()->lte('n.startDate', ':now'),
                    $query->expr()->gte('n.endDate', ':now'),
                    $query->expr()->eq('n.active', 'true')
                )
            )
            ->setParameter('now', $now)
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();

        return $resultSet;
    }
}
