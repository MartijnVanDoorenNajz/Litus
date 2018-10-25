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

namespace CudiBundle\Repository\Sale\Article;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;
use CudiBundle\Entity\Sale\Article;

/**
 * Restriction
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Restriction extends EntityRepository
{
    /**
     * @param  Article             $article
     * @return \Doctrine\ORM\Query
     */
    public function findAllByArticleQuery(Article $article)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('CudiBundle\Entity\Sale\Article\Restriction', 'r')
            ->innerJoin('r.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.article', ':article'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('article', $article)
            ->getQuery();

        return $resultSet;
    }
}
