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

namespace CudiBundle\Repository\Stock\Period\Value;

use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Stock\Period;

/**
 * Start
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Start extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  Article $article
     * @param  Period  $period
     * @return \CudiBundle\Entity\Stock\Period\Value\Start|null
     */
    public function findOneByArticleAndPeriod(Article $article, Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('v')
            ->from('CudiBundle\Entity\Stock\Period\Value\Start', 'v')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('v.article', ':article'),
                    $query->expr()->eq('v.period', ':period')
                )
            )
            ->setParameter('article', $article->getId())
            ->setParameter('period', $period->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param  Article $article
     * @param  Period  $period
     * @return integer
     */
    public function findValueByArticleAndPeriod(Article $article, Period $period)
    {
        $value = $this->findOneByArticleAndPeriod($article, $period);

        if ($value == null) {
            return 0;
        }

        return $value->getValue();
    }

    public function findAllByPeriod(Period $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('v')
            ->from('CudiBundle\Entity\Stock\Period\Value\Start', 'v')
            ->where(
                $query->expr()->eq('v.period', ':period')
            )
            ->setParameter('period', $period->getId())
            ->getQuery()
            ->getResult();
    }
}
