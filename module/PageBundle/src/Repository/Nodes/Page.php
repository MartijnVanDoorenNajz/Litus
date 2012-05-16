<?php

namespace PageBundle\Repository\Nodes;

use Doctrine\ORM\EntityRepository;

/**
 * Page
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Page extends EntityRepository
{
    public function findAll()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
        	->from('PageBundle\Entity\Nodes\Page', 'p')
        	->orderBy('p.updateTime', 'DESC')
        	->getQuery()
        	->getResult();
        
        return $resultSet;
    }
}