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

namespace LogisticsBundle\Repository\Order;

use Doctrine\ORM\Query;
use LogisticsBundle\Entity\Article as ArticleEntity;
use LogisticsBundle\Entity\Order as OrderEntity;

/**
 * OrderArticleMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderArticleMap extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{

    public function findAllByOrderQuery(OrderEntity $order)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m', 'a')
            ->from('SyllabusBundle\Entity\Order\StudyMap', 'm')
            ->innerJoin('m.article', 'a')
            ->where(
                    $query->expr()->eq('m.order', ':order'),
            )
            ->setParameter('order', $order)
            ->getQuery();
    }

    public function findOneByOrderArticle(OrderEntity $order, ArticleEntity $article)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('SyllabusBundle\Entity\Order\StudyMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.order', ':order'),
                    $query->expr()->eq('m.article', ':article')
                )
            )
            ->setParameter('order', $order)
            ->setParameter('article', $article)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findMapsFromStudyQuery(ArticleEntity $article)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('SyllabusBundle\Entity\Order\StudyMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.article', ':article')
                )
            )
            ->setParameter('article', $article)
            ->getQuery();
    }

}
