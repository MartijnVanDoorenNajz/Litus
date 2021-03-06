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

namespace BrBundle\Repository\Invoice;

use BrBundle\Entity\Collaborator;
use BrBundle\Entity\Company;

/**
 * Manual
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Manual extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findInvoiceAuthors()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('DISTINCT(i.author)')
            ->from('BrBundle\Entity\Invoice\Manual', 'i')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findInvoiceCompanies()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('DISTINCT(i.company)')
            ->from('BrBundle\Entity\Invoice\Manual', 'i')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  Collaborator $person
     * @return \Doctrine\ORM\Query
     */
    public function findAllByAuthor(Collaborator $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i')
            ->from('BrBundle\Entity\Invoice\Manual', 'i')
            ->where(
                $query->expr()->eq('i.author', ':person')
            )
            ->setParameter('person', $person)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  Company $company
     * @return \Doctrine\ORM\Query
     */
    public function findAllByCompany(Company $company)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i')
            ->from('BrBundle\Entity\Invoice\Manual', 'i')
            ->where(
                $query->expr()->eq('i.company', ':company')
            )
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
    }
}
