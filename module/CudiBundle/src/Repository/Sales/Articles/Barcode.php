<?php

namespace CudiBundle\Repository\Sales\Articles;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join;

/**
 * Barcode
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Barcode extends EntityRepository
{
    public function findOneByBarcodeAndAcademicYear($barcode, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('b')
            ->from('CudiBundle\Entity\Sales\Articles\Barcode', 'b')
            ->innerJoin('b.article', 'a', Join::WITH,
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.academicYear', ':academicYear')
                )
            )
            ->where(
                $query->expr()->eq('b.barcode', ':barcode')
            )
            ->setParameter('barcode', $barcode)
            ->setParameter('academicYear', $academicYear->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
