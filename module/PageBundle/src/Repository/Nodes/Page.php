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
            ->where('p.endTime is null')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findByCategory($category)
    {
        return $this->_em->getRepository('PageBundle\Entity\Nodes\Page')
            ->findBy(array('category' => $category, 'endTime' => null));
    }

    public function findByParent($parent)
    {
        return $this->_em->getRepository('PageBundle\Entity\Nodes\Page')
            ->findBy(array('parent' => $parent, 'endTime' => null));
    }
}
