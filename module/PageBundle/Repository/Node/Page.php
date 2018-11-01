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

namespace PageBundle\Repository\Node;

use PageBundle\Entity\Node\Page as PageEntity;

/**
 * Page
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Page extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->where(
                $query->expr()->isNull('p.endTime')
            )
            ->getQuery();
    }

    public function findByCategory($category)
    {
        return $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findBy(array('category' => $category, 'endTime' => null));
    }

    public function findByParent($parent)
    {
        return $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findBy(array('parent' => $parent, 'endTime' => null));
    }

    public function findOneByNameAndParent($name, $parentName)
    {
        if ($parentName === null) {
            return $this->findOneByName($name, null);
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->innerJoin('p.parent', 'par')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.name', ':name'),
                    $query->expr()->eq('par.name', ':parentName'),
                    $query->expr()->isNull('p.endTime')
                )
            )
            ->setParameter('name', $name)
            ->setParameter('parentName', $parentName)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByName($name, PageEntity $parent = null)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.name', ':name'),
                    $parent === null ? $query->expr()->isNull('p.parent') : $query->expr()->eq('p.parent', ':parent'),
                    $query->expr()->isNull('p.endTime')
                )
            )
            ->setParameter('name', $name);

        if ($parent !== null) {
            $query->setParameter('parent', $parent);
        }

        return $query->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByTitleQuery($title)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $translations = $query->select('p.id')
            ->from('PageBundle\Entity\Node\Translation', 't')
            ->innerJoin('t.page', 'p')
            ->where(
                $query->expr()->like($query->expr()->lower('t.title'), ':title')
            )
            ->setParameter('title', '%' . strtolower($title) . '%')
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach ($translations as $translation) {
            $ids[] = $translation['id'];
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('p.id', $ids),
                    $query->expr()->isNull('p.endTime')
                )
            )
            ->getQuery();
    }
}
