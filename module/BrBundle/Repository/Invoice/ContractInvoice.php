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

use BrBundle\Entity\Order,
    Doctrine\ORM\EntityRepository;

/**
 * ContractInvoice
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContractInvoice extends EntityRepository
{
    /**
     * @param  Order               $order
     * @return \Doctrine\ORM\Query
     */
    public function findAllByOrderQuery(Order $order)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $result = $query->select('i')
            ->from('BrBundle\Entity\Invoice', 'i')
            ->where(
                $query->expr()->eq('i.order', ':order')
            )
            ->setParameter('order', $order)
            ->getQuery();

        return $result;
    }
}
