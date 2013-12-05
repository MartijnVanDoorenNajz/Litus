<?php

namespace CudiBundle\Repository;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Article
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Article extends EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->orderBy('a.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByTitleQuery($title)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where($query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('a.title'), ':title'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->setParameter('title', '%'.strtolower($title).'%')
            ->orderBy('a.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByAuthorQuery($author)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('a.authors'), ':author'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->setParameter('author', '%'.strtolower($author).'%')
            ->orderBy('a.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByISBNQuery($isbn)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->concat('a.isbn', '\'\''), ':isbn'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->setParameter('isbn', '%'.strtolower($isbn).'%')
            ->orderBy('a.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByPublisherQuery($publisher)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('a.publishers'), ':publisher'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->setParameter('publisher', '%'.strtolower($publisher).'%')
            ->orderBy('a.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllBySubjectQuery($subject, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $subjects = $query->select('s.id')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                    $query->expr()->like($query->expr()->lower('s.code'), ':name')
                )
            )
            ->setParameter('name', strtolower(trim($subject)) . '%')
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach($subjects as $subject)
            $ids[] = $subject['id'];

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a.id')
            ->from('CudiBundle\Entity\Article\SubjectMap', 's')
            ->innerJoin('s.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('s.subject', $ids),
                    $query->expr()->eq('s.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        $articles = array();
        foreach($resultSet as $mapping)
            $articles[] = $mapping->getArticle()->getId();

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('a.id', $articles),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->getQuery();

        return $resultSet;
    }

    public function findAllByProf(Person $person)
    {
        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findByProf($person);

        $ids = array(0);
        foreach($subjects as $subject)
            $ids[] = $subject->getSubject()->getId();

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('CudiBundle\Entity\Article\SubjectMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.removed', 'false'),
                    $query->expr()->in('m.subject', $ids)
                )
            )
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach($resultSet as $mapping) {
            $edited = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndPreviousIdAndAction('article', $mapping->getArticle()->getId(), 'edit');

            if (isset($edited[0]) && !$edited[0]->isRefused()) {
                $ids[] = $edited[0]->getEntityId();
            } else {
                $ids[] = $mapping->getArticle()->getId();
            }
        }

        $added = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Prof\Action')
            ->findAllByEntityAndActionAndPerson('article', 'add', $person);

        foreach($added as $add) {
            $edited = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndPreviousIdAndAction('article', $add->getEntityId(), 'edit');

            if (isset($edited[0]) && !$edited[0]->isRefused()) {
                $ids[] = $edited[0]->getEntityId();
            } else {
                $ids[] = $add->getEntityId();
            }
        }

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('a.id', $ids)
                )
            )
            ->orderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult();

        $articles = array();
        foreach($resultSet as $article) {
            if (!$article->isInternal() || $article->isOfficial())
                $articles[] = $article;
        }

        return $articles;
    }

    public function findOneByIdAndProf($id, Person $person)
    {
        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findByProf($person);

        $ids = array(0);
        foreach($subjects as $subject)
            $ids[] = $subject->getSubject()->getId();

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('CudiBundle\Entity\Article\SubjectMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.removed', 'false'),
                    $query->expr()->eq('m.article', ':id'),
                    $query->expr()->in('m.subject', $ids)
                )
            )
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($resultSet &&
                (!$resultSet->getArticle()->isInternal() || $resultSet->getArticle()->isOfficial()))
            return $resultSet->getArticle();

        $actions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Prof\Action')
            ->findAllByEntityAndEntityIdAndPerson('article', $id, $person);

        if (isset($actions[0]))
            return $actions[0]->setEntityManager($this->_em)
                ->getEntity();

        return null;
    }
}
