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

namespace ApiBundle\Controller;

use DateInterval,
    DateTime,
    IntlDateFormatter,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * @author Koen Certyn
 */
class ShiftController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function myShiftAction()
    {
        //TODO key needs to be given and person needs to be get from the key
        //$authenticatedPerson = $key->getPerson();

        //-----DUMMYCODE-----
        $authenticatedPerson = null;
        //---END DUMMYCODE---

        if ($authenticatedPerson != null) {
            $myShifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($authenticatedPerson);
        } else {
            return new ViewModel();
        }

        foreach ($myShifts as $shift) {
            $result[] = array(
                'name' => $shift->getName(),
                'discription' => $shift->getDiscription(),
                'startDate' => $shift->getStartDate(),
                'endDate' => $shift->getEndDate(),
                'manager' => $shift->getManager(),
                );
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );

    }

}