<?php

namespace BrBundle\Repository\Company;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityRepository;

/**
 * Page
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Page extends EntityRepository
{

    public function findOneActiveBySlug($slug, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('BrBundle\Entity\Company\Page', 'p')
            ->innerJoin('p.years', 'y')
            ->innerJoin('p.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('c.active', 'true'),
                    $query->expr()->eq('c.slug', ':slug'),
                    $query->expr()->eq('y', ':year')
                )
            )
            ->setParameter('slug', $slug)
            ->setParameter('year', $academicYear)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findAllActive(AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('BrBundle\Entity\Company\Page', 'p')
            ->innerJoin('p.years', 'y')
            ->innerJoin('p.company', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('c.active', 'true'),
                    $query->expr()->eq('y', ':year')
                )
            )
            ->setParameter('year', $academicYear)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
