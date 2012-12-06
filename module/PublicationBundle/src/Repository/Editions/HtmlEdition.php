<?php

namespace PublicationBundle\Repository\Editions;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityRepository,
	PublicationBundle\Entity\Publication as PublicationEntity;

/**
 * HtmlEdition
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HtmlEdition extends EntityRepository
{
    public function findAllByPublicationAndAcademicYear(PublicationEntity $publication, AcademicYear $academicYear)
	{
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PublicationBundle\Entity\Editions\Html', 'p')
            ->where(
                $query->expr()->andX(
                   $query->expr()->eq('p.publication', ':publication'),
                   $query->expr()->eq('p.academicYear', ':year')
               )
            )
            ->setParameter('publication', $publication)
            ->setParameter('year', $academicYear)
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findOneByPublicationTitleAndAcademicYear(PublicationEntity $publication, $title, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('PublicationBundle\Entity\Editions\Html', 'p')
            ->where(
                $query->expr()->andX(
                   $query->expr()->eq('p.publication', ':publication'),
                   $query->expr()->eq('p.title', ':title'),
                   $query->expr()->eq('p.academicYear', ':year')
               )

            )
            ->setParameter('publication', $publication)
            ->setParameter('title', $title)
            ->setParameter('year', $academicYear)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
