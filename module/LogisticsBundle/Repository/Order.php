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

namespace LogisticsBundle\Repository;

use LogisticsBundle\Entity\Article as ArticleEntity;

use DateTime;

/**
 * Order
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Order extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllOldQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from('LogisticsBundle\Entity\Order', 'o')
            ->where(
                $query->expr()->lt('o.endDate', ':now')
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->getQuery();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllActiveQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from('LogisticsBundle\Entity\Order', 'o')
            ->where(
                $query->expr()->gt('o.endDate', ':now')
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->getQuery();
    }

    /**
     * @param  integer $id
     * @return \LogisticsBundle\Entity\Order
     */
    public function findOneActiveById($type, $id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from('LogisticsBundle\Entity\Order', 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('o.id', ':id'),
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.removed', 'FALSE')
                )
            )
            ->setParameter('id', $id)
            ->setParameter('now', new DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param  ArticleEntity $article
     * @return \Doctrine\ORM\Query
     */
    public function findAllByArticleQuery(ArticleEntity $article)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from('LogisticsBundle\Entity\order', 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('o.removed', 'FALSE'),
                    $query->expr()->eq('o.approved', 'TRUE')
                )
            )
            ->innerJoin('LogisticsBundle\Entity\Order\OrderArticleMap', 'oam')
            ->where(
                $query->expr()->eq('oam.article', ':article')
            )
            ->setParameter('article', $article->getId())
            ->orderBy('o.name', 'ASC')
            ->getQuery();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllActiveByNameQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('v, c')
            ->from('LogisticsBundle\Entity\Order', 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.removed', 'FALSE'),
                    $query->expr()->eq('o.approved', 'TRUE')
                )
            )
            ->setParameter('now', new DateTime())
            ->orderBy('o.name', 'ASC')
            ->getQuery();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllActiveByUpdateDateQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('v, c')
            ->from('LogisticsBundle\Entity\Order', 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.removed', 'FALSE'),
                    $query->expr()->eq('o.approved', 'TRUE')
                )
            )
            ->setParameter('now', new DateTime())
            ->orderBy('o.dateUpdated', 'DESC')
            ->getQuery();
    }

//    /**
//     * @param  string $type
//     * @param  string $sector
//     * @param  string $location
//     * @param  string $master
//     * @return \Doctrine\ORM\Query
//     */
//    public function findAllActiveByTypeQuery($type, $sector, $location, $master)
//    {
//        $query = $this->getEntityManager()->createQueryBuilder();
//        $query->select('v, c')
//            ->from('LogisticsBundle\Entity\Company\Job', 'v')
//            ->innerJoin('v.company', 'c')
//            ->where(
//                $query->expr()->andx(
//                    $query->expr()->eq('v.type', ':type'),
//                    $query->expr()->gt('v.endDate', ':now'),
//                    $query->expr()->eq('c.active', 'true'),
//                    $query->expr()->eq('v.removed', 'FALSE'),
//                    $query->expr()->eq('v.approved', 'TRUE')
//                )
//            );
//
//        if ($sector !== null) {
//            $query->andWhere(
//                $query->expr()->eq('v.sector', ':sector')
//            )
//                ->setParameter('sector', $sector);
//        }
//
//        if ($location !== null) {
//            $query->andWhere(
//                $query->expr()->eq('v.location', ':location')
//            )
//                ->setParameter('location', $location);
//        }
//
//        if ($master !== null) {
//            $query->andWhere(
//                $query->expr()->eq('v.master', ':master')
//            )
//                ->setParameter('master', $master);
//        }
//
//        return $query->setParameter('type', $type)
//            ->setParameter('now', new DateTime())
//            ->orderBy('c.name', 'ASC')
//            ->addOrderBy('v.name', 'ASC')
//            ->getQuery();
//    }

//    /**
//     * @param  string $type
//     * @param  string $sector
//     * @param  string $location
//     * @param  string $master
//     * @return \Doctrine\ORM\Query
//     */
//    public function findAllActiveByTypeSortedByDateQuery($type, $sector, $location, $master)
//    {
//        $query = $this->getEntityManager()->createQueryBuilder();
//        $query->select('v, c')
//            ->from('LogisticsBundle\Entity\Company\Job', 'v')
//            ->innerJoin('v.company', 'c')
//            ->where(
//                $query->expr()->andx(
//                    $query->expr()->eq('v.type', ':type'),
//                    $query->expr()->gt('v.endDate', ':now'),
//                    $query->expr()->eq('c.active', 'true'),
//                    $query->expr()->eq('v.removed', 'FALSE'),
//                    $query->expr()->eq('v.approved', 'TRUE')
//                )
//            );
//        if ($sector !== null) {
//            $query->andWhere(
//                $query->expr()->eq('v.sector', ':sector')
//            )
//                ->setParameter('sector', $sector);
//        }
//
//        if ($location !== null) {
//            $query->andWhere(
//                $query->expr()->eq('v.location', ':location')
//            )
//                ->setParameter('location', $location);
//        }
//
//        if ($master !== null) {
//            $query->andWhere(
//                $query->expr()->eq('v.master', ':master')
//            )
//                ->setParameter('master', $master);
//        }
//
//        return $query->setParameter('type', $type)
//            ->setParameter('now', new DateTime())
//            ->orderBy('v.dateUpdated', 'DESC')
//            ->getQuery();
//    }
//
//    /**
//     * @param  string $type
//     * @param  string $sector
//     * @param  string $location
//     * @param  string $master
//     * @return \Doctrine\ORM\Query
//     */
//    public function findAllActiveByTypeSortedByJobNameQuery($type, $sector, $location, $master)
//    {
//        $query = $this->getEntityManager()->createQueryBuilder();
//        $query->select('v, c')
//            ->from('LogisticsBundle\Entity\Company\Job', 'v')
//            ->innerJoin('v.company', 'c')
//            ->where(
//                $query->expr()->andx(
//                    $query->expr()->eq('v.type', ':type'),
//                    $query->expr()->gt('v.endDate', ':now'),
//                    $query->expr()->eq('c.active', 'true'),
//                    $query->expr()->eq('v.removed', 'FALSE'),
//                    $query->expr()->eq('v.approved', 'TRUE')
//                )
//            );
//
//        if ($sector !== null) {
//            $query->andWhere(
//                $query->expr()->eq('v.sector', ':sector')
//            )
//                ->setParameter('sector', $sector);
//        }
//
//        if ($location !== null) {
//            $query->andWhere(
//                $query->expr()->eq('v.location', ':location')
//            )
//                ->setParameter('location', $location);
//        }
//
//        if ($master !== null) {
//            $query->andWhere(
//                $query->expr()->eq('v.master', ':master')
//            )
//                ->setParameter('master', $master);
//        }
//
//        return $query->setParameter('type', $type)
//            ->setParameter('now', new DateTime())
//            ->orderBy('v.name', 'ASC')
//            ->getQuery();
//    }
}
