<?php

namespace CudiBundle\Repository\Sale\Article\Discount;

use CudiBundle\Entity\Sale\Article,
    CommonBundle\Entity\General\Organization,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Discount
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Discount extends EntityRepository
{
    public function findOneByArticleAndType(Article $article, $type)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Sale\Article\Discount\Discount', 'd')
            ->leftJoin('d.template', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
                    $query->expr()->orX(
                        $query->expr()->eq('d.type', ':type'),
                        $query->expr()->eq('t.type', ':type')
                    )
                )
            )
            ->setParameter('article', $article)
            ->setParameter('type', $type)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findOneByArticleAndTypeAndOrganization(Article $article, $type, Organization $organization = null)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('d')
            ->from('CudiBundle\Entity\Sale\Article\Discount\Discount', 'd')
            ->leftJoin('d.template', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
                    $query->expr()->orX(
                        $query->expr()->eq('d.type', ':type'),
                        $query->expr()->eq('t.type', ':type')
                    ),
                    $query->expr()->orX(
                        $organization == null ? $query->expr()->isNull('d.organization') : $query->expr()->eq('d.organization', ':organization'),
                        $organization == null ? $query->expr()->isNull('t.organization') : $query->expr()->eq('t.organization', ':organization')
                    )
                )
            )
            ->setParameter('article', $article->getId())
            ->setParameter('type', $type);

        if ($organization != null)
            $query->setParameter('organization', $organization);

        $resultSet = $query->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findAllByArticleQuery(Article $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Sale\Article\Discount\Discount', 'd')
            ->innerJoin('d.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
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