<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Form\Admin\Sales\Session;

use CommonBundle\Entity\General\Bank\CashRegister,
    CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Close Sale Session
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Close extends Add
{
    public function __construct(EntityManager $entityManager, CashRegister $cashRegister, $options = null )
    {
        parent::__construct($entityManager, $options);

        $this->removeElement('submit');

        $field = new Submit('submit');
        $field->setLabel('Close')
            ->setAttrib('class', 'sale_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populateFromCashRegister($cashRegister);
    }
}
