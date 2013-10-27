<?php

namespace FormBundle\Repository;

use DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Viewermap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ViewerMap extends EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->getQuery();

        return $resultSet;
    }

    public function findOneById($id) {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->where(
                $query->expr()->eq('n.id', ':id')
            )
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findOneByPersonAndForm($person, $form) {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->innerJoin('n.form', 'f')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('n.person', ':person'),
                    $query->expr()->eq('n.form', ':form')
                )
            )
            ->setParameter('person', $person)
            ->setParameter('form', $form)
            ->orderBy('f.startDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findAllByFormQuery($formId) {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->where(
                $query->expr()->eq('n.form', ':id')
            )
            ->setParameter('id', $formId)
            ->getQuery();

        return $resultSet;
    }

    public function findAllByPersonQuery($person) {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->innerJoin('n.form', 'f')
            ->where(
                $query->expr()->eq('n.person', ':person')
            )
            ->setParameter('person', $person)
            ->orderBy('f.startDate', 'DESC')
            ->getQuery();

        return $resultSet;
    }
}
