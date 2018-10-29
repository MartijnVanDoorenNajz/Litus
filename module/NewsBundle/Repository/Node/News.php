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

namespace NewsBundle\Repository\Node;

use DateTime;

/**
 * News
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class News extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('NewsBundle\Entity\Node\News', 'n')
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();
    }

    public function findAllSiteQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('NewsBundle\Entity\Node\News', 'n')
            ->where(
                $query->expr()->orX(
                    $query->expr()->gte('n.endDate', ':now'),
                    $query->expr()->isNull('n.endDate')
                )
            )
            ->setParameter('now', new DateTime())
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();
    }

    public function findApiQuery(DateTime $maxAge)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('NewsBundle\Entity\Node\News', 'n')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('n.creationTime', ':maxAge'),
                    $query->expr()->orX(
                        $query->expr()->gte('n.endDate', ':now'),
                        $query->expr()->isNull('n.endDate')
                    )
                )
            )
            ->setParameter('now', new DateTime())
            ->setParameter('maxAge', $maxAge)
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();
    }

    public function findNbSiteQuery($nbResults, DateTime $maxAge)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('NewsBundle\Entity\Node\News', 'n')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('n.creationTime', ':maxAge'),
                    $query->expr()->orX(
                        $query->expr()->gte('n.endDate', ':now'),
                        $query->expr()->isNull('n.endDate')
                    )
                )
            )
            ->setParameter('now', new DateTime())
            ->setParameter('maxAge', $maxAge)
            ->orderBy('n.creationTime', 'DESC')
            ->setMaxResults($nbResults)
            ->getQuery();
    }
}
