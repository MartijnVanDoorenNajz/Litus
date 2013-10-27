<?php

namespace FormBundle\Repository;

use DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Field
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Field extends EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Field', 'n')
            ->getQuery();

        return $resultSet;
    }

    public function findOneById($id) {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Field', 'n')
            ->where(
                $query->expr()->eq('n.id', ':id')
            )
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findAllByFormQuery($formId) {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Field', 'n')
            ->where(
                $query->expr()->eq('n.form', ':id')
            )
            ->setParameter('id', $formId)
            ->orderBy('n.order', 'ASC')
            ->getQuery();

        return $resultSet;
    }
}
