<?php

namespace CudiBundle\Repository\Stock\Order;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Stock\Order\Item as ItemEntity,
    CudiBundle\Entity\Stock\Order\Order as OrderEntity,
    CudiBundle\Entity\Stock\Period,
    CudiBundle\Entity\Supplier,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Order
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Order extends EntityRepository
{
    public function findAllBySupplierAndPeriodQuery(Supplier $supplier, Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('o')
            ->from('CudiBundle\Entity\Stock\Order\Order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('o.supplier', ':supplier'),
                    $query->expr()->gt('o.dateCreated', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('o.dateCreated', ':endDate')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('o.dateCreated', 'DESC');

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery();

        return $resultSet;
    }

    public function findOneOpenBySupplier(Supplier $supplier)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('o')
            ->from('CudiBundle\Entity\Stock\Order\Order', 'o')
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

        return $resultSet;
    }

    public function addNumberByArticle(Article $article, $number, Person $person)
    {
        $item = $this->_em
            ->getRepository('CudiBundle\Entity\Stock\Order\Item')
            ->findOneOpenByArticle($article);

        if (isset($item)) {
            $item->setNumber($item->getNumber() + $number);
        } else {
            $order = $this->findOneOpenBySupplier($article->getSupplier());
            if (null === $order) {
                $order = new OrderEntity($article->getSupplier(), $person);
                $this->_em->persist($order);
            }

            $item = new ItemEntity($article, $order, $number);
            $this->_em->persist($item);
        }

        return $item;
    }

    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('o')
            ->from('CudiBundle\Entity\Stock\Order\Order', 'o')
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

        return $resultSet;
    }

    public function findAllBySupplierAndAcademicYearQuery($supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('o')
            ->from('CudiBundle\Entity\Stock\Order\Order', 'o')
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
            ->setParameter('supplier', '%'.strtolower($supplier).'%')
            ->orderBy('o.dateOrdered', 'DESC')
            ->getQuery();

        return $resultSet;
    }
}
