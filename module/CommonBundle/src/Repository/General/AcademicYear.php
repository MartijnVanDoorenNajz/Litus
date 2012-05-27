<?php

namespace CommonBundle\Repository\General;

use Doctrine\ORM\EntityRepository;

/**
 * AcademicYear
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AcademicYear extends EntityRepository
{
    public function findAll()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('y')
        	->from('CommonBundle\Entity\General\AcademicYear', 'y')
        	->orderBy('y.startDate')
        	->getQuery()
        	->getResult();
        
        return $resultSet;
    }
}