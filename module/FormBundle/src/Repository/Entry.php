<?php

namespace FormBundle\Repository;

use DateTime,
    Doctrine\ORM\EntityRepository;

/**
 * Entry
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Entry extends EntityRepository
{
    public function findAll()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Entry', 'n')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByField($field)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Entry', 'n')
            ->where(
                $query->expr()->eq('n.field', ':field')
            )
            ->setParameter('field', $field)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByFormEntry($formEntry)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Entry', 'n')
            ->where(
                $query->expr()->eq('n.formEntry', ':formEntry')
            )
            ->setParameter('formEntry', $formEntry)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findOneByFormEntryAndField($formEntry, $field)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Entry', 'n')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('n.field', ':field'),
                    $query->expr()->eq('n.formEntry', ':form')
                )
            )
            ->setParameter('field', $field)
            ->setParameter('form', $formEntry)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
