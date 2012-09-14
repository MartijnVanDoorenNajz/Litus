<?php

namespace FormBundle\Repository\Nodes;

use DateTime,
    Doctrine\ORM\EntityRepository;

/**
 * FormSpecification
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FormSpecification extends EntityRepository
{
    public function findAll()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Nodes\FormSpecification', 'n')
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findOneById($id) {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Nodes\FormSpecification', 'n')
            ->where(
                $query->expr()->eq('n.id', ':id')
            )
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
