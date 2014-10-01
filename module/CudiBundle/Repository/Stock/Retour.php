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

namespace CudiBundle\Repository\Stock;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Stock\Period as PeriodEntity,
    CudiBundle\Entity\Supplier;

/**
 * Retour
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Retour extends EntityRepository
{
    public function findAllBySupplierAndPeriodQuery(Supplier $supplier, PeriodEntity $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('r')
            ->from('CudiBundle\Entity\Stock\Retour', 'r')
            ->innerJoin('r.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->gt('r.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('r.timestamp', ':endDate')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('r.timestamp', 'DESC');

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        $resultSet = $query->getQuery();

        return $resultSet;
    }

    public function findTotalByArticleAndPeriod(Article $article, PeriodEntity $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('SUM(r.number)')
            ->from('CudiBundle\Entity\Stock\Retour', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.article', ':article'),
                    $query->expr()->gt('r.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('r.timestamp', ':endDate')
                )
            )
            ->setParameter('article', $article)
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        $resultSet = $query->getQuery()
            ->getSingleScalarResult();

        return $resultSet ? $resultSet : 0;
    }

    public function findAllByPeriodQuery(PeriodEntity $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('r')
            ->from('CudiBundle\Entity\Stock\Retour', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('r.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('r.timestamp', ':endDate')
                )
            )
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('r.timestamp', 'DESC');

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        $resultSet = $query->getQuery();

        return $resultSet;
    }
}
