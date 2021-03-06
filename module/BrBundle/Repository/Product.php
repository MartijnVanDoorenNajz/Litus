<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Repository;

/**
 * Product
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Product extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  integer $id
     * @return \Doctrine\ORM\Query
     */
    public function findProductByIdQuery($id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('BrBundle\Entity\Product', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.id', ':id'),
                    $query->expr()->eq('p.old', 'FALSE')
                )
            )
//            ->orderBy('p.name', 'ASC')
            ->setParameter('id', $id)
            ->getQuery();
    }

    /**
     * @param  string $name
     * @return \Doctrine\ORM\Query
     */
    public function findProductByNameNotOld($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('BrBundle\Entity\Product', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.name', ':name'),
                    $query->expr()->eq('p.old', 'FALSE')
                )
            )
            ->setParameter('name', $name)
//            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllNotOld()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('BrBundle\Entity\Product', 'p')
            ->where(
                $query->expr()->eq('p.old', 'FALSE')
            )
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
