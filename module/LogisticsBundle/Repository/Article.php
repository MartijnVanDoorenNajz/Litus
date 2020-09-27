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

namespace LogisticsBundle\Repository;

use LogisticsBundle\Entity\Article as ArticleEntity;

/**
 * Article
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Article extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->like($query->expr()->lower('a.name'), ':name')
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setMaxResults(20)
            ->getQuery();
    }

    public function findAllByVisibilityQuery($visibility)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->like($query->expr()->lower('a.visibility'), ':visibility')
            )
            ->setParameter('visibility', '%' . strtolower($visibility) . '%')
            ->setMaxResults(20)
            ->getQuery();
    }

    public function findAllByStatusQuery($status)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->like($query->expr()->lower('a.status'), ':status')
            )
            ->setParameter('status', '%' . strtolower($status) . '%')
            ->setMaxResults(20)
            ->getQuery();
    }
}
