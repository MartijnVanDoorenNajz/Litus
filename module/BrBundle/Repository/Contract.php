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
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Repository;

use BrBundle\Entity\Company as Comp,
    BrBundle\Entity\Collaborator,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Contract
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Contract extends EntityRepository
{
    public function findAllContractIds()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('c.id')
            ->from('BrBundle\Entity\Contract', 'c')
            ->getQuery();

        $return = array();
        foreach ($resultSet as $result)
            $return[] = $result['id'];

        return $return;
    }

    public function findCurrentVersionNb($id)
    {
        $query = $this->_em->createQueryBuilder();
        $highestVersionNb = $query->select('MAX(e.version)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->innerjoin('c.contractEntries','e')
            ->where(
                $query->expr()->eq('c.id', ':id')
            )
            ->getQuery()
            ->setParameter('id', $id)
            ->getSingleScalarResult();

        return $highestVersionNb;
    }

    public function findNextInvoiceNb()
    {
        $query = $this->_em->createQueryBuilder();
        $highestInvoiceNb = $query->select('MAX(c.invoiceNb)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->getQuery()
            ->getSingleScalarResult();

        return ++$highestInvoiceNb;
    }

    public function findNextContractNb()
    {
        $query = $this->_em->createQueryBuilder();
        $highestContractNb = $query->select('MAX(c.contractNb)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->getQuery()
            ->getSingleScalarResult();

        return ++$highestContractNb;
    }

    public function findContractsByAuthorIDQuery(Collaborator $person)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('c')
            ->from('BrBundle\Entity\Contract', 'c')
            ->innerjoin('c.order','o')
            ->where(
                $query->expr()->eq('c.author', ':person'),
                $query->expr()->eq('o.old', 'FALSE')
            )
            ->setParameter('person', $person)
            ->getQuery();

        return $result;
    }

    public function findAuthorByIDQuery($id)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('p')
            ->from('BrBundle\Entity\Collaborator', 'p')
            ->where(
                $query->expr()->eq('p.id', ':id')
            )
            ->setParameter('id', $id)
            ->getQuery();

        return $result;
    }

    public function getContractAmountByPerson(Collaborator $person)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('count(c)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->where(
                $query->expr()->eq('c.author', ':person')
            )
            ->setParameter('person', $person)
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function getContractedRevenueByPerson(Collaborator $person)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('sum(o.totalCost)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->innerjoin('c.order','o')
            ->where(
                $query->expr()->eq('c.author', ':person')
            )
            ->setParameter('person', $person)
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function getPaidRevenueByPerson(Collaborator $person)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('sum(o.totalCost)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->innerjoin('c.order','o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('c.author', ':person'),
                    $query->expr()->eq('c.signed', 'TRUE')
                )
            )
            ->setParameter('person', $person)
            ->getQuery()
            ->getSingleScalarResult();

        if($result == '')
            return 0;
        return $result;
    }

    public function findContractAuthorsQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('distinct(c.author)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->getQuery();

        return $result;
    }

    public function findContractsByCompanyIDQuery(Comp $company)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('c')
            ->from('BrBundle\Entity\Contract', 'c')
            ->innerjoin('c.order','o')
            ->where(
                $query->expr()->eq('c.company', ':company'),
                $query->expr()->eq('o.old', 'FALSE')
            )
            ->setParameter('company', $company)
            ->getQuery();

        return $result;
    }

    public function findCompanyByIDQuery($id)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('c')
            ->from('BrBundle\Entity\Company', 'c')
            ->where(
                $query->expr()->eq('c.id', ':id')
            )
            ->setParameter('id', $id)
            ->getQuery();

        return $result;
    }

    public function getContractAmountByCompany(Comp $company)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('count(c)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->where(
                $query->expr()->eq('c.company', ':company')
            )
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function getContractedRevenueByCompany(Comp $company)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('sum(o.totalCost)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->innerjoin('c.order','o')
            ->where(
                $query->expr()->eq('c.company', ':company')
            )
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function getPaidRevenueByCompany(Comp $company)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('sum(o.totalCost)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->innerjoin('c.order','o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('c.company', ':company'),
                    $query->expr()->eq('c.signed', 'TRUE')
                )
            )
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();

        if($result == '')
            return 0;
        return $result;
    }

    public function findContractCompanyQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('distinct(c.company)')
            ->from('BrBundle\Entity\Contract', 'c')
            ->getQuery();

        return $result;
    }
}
