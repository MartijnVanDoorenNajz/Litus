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

namespace CudiBundle\Repository\Stock;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Stock\Order as OrderEntity;
use CudiBundle\Entity\Stock\Order\Item as ItemEntity;
use CudiBundle\Entity\Stock\Period;
use CudiBundle\Entity\Supplier;

/**
 * Order
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Order extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  Supplier $supplier
     * @param  Period   $period
     * @return \Doctrine\ORM\Query
     */
    public function findAllBySupplierAndPeriodQuery(Supplier $supplier, Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('o')
            ->from('CudiBundle\Entity\Stock\Order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('o.supplier', ':supplier'),
                    $query->expr()->orX(
                        $query->expr()->andX(
                            $query->expr()->gt('o.dateOrdered', ':startDate'),
                            $period->isOpen() ? '1=1' : $query->expr()->lt('o.dateOrdered', ':endDate')
                        ),
                        $query->expr()->isNull('o.dateOrdered')
                    )
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('o.dateCreated', 'DESC');

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        return $query->getQuery();
    }

    /**
     * @param  Supplier $supplier
     * @return \CudiBundle\Entity\Stock\Order|null
     */
    public function findOneOpenBySupplier(Supplier $supplier)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from('CudiBundle\Entity\Stock\Order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('o.supplier', ':supplier'),
                    $query->expr()->isNull('o.dateOrdered')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param  Article $article
     * @param  integer $number
     * @param  Person  $person
     * @return ItemEntity
     */
    public function addNumberByArticle(Article $article, $number, Person $person)
    {
        $item = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Order\Item')
            ->findOneOpenByArticle($article);

        if (isset($item)) {
            $item->setNumber($item->getNumber() + $number);
        } else {
            $order = $this->findOneOpenBySupplier($article->getSupplier());
            if ($order === null) {
                $order = new OrderEntity($article->getSupplier(), $person);
                $this->getEntityManager()->persist($order);
            }

            $item = new ItemEntity($article, $order, $number);
            $this->getEntityManager()->persist($item);
        }

        return $item;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from('CudiBundle\Entity\Stock\Order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->isNotNull('o.dateOrdered'),
                    $query->expr()->gt('o.dateOrdered', ':start'),
                    $query->expr()->lt('o.dateOrdered', ':end')
                )
            )
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('o.dateOrdered', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string       $supplier
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllBySupplierAndAcademicYearQuery($supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from('CudiBundle\Entity\Stock\Order', 'o')
            ->innerJoin('o.supplier', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':supplier'),
                    $query->expr()->isNotNull('o.dateOrdered'),
                    $query->expr()->gt('o.dateOrdered', ':start'),
                    $query->expr()->lt('o.dateOrdered', ':end')
                )
            )
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->setParameter('supplier', '%' . strtolower($supplier) . '%')
            ->orderBy('o.dateOrdered', 'DESC')
            ->getQuery();
    }
}
