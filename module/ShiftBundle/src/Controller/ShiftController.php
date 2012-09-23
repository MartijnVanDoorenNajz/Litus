<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    ShiftBundle\Entity\Shifts\Responsible,
    ShiftBundle\Entity\Shifts\Volunteer,
    ShiftBundle\Form\Shift\Search\Event as EventSearchForm,
    ShiftBundle\Form\Shift\Search\Unit as UnitSearchForm,
    Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * Flight Mode
 * This file was edited by Pieter Maene while in flight from Vienna to Brussels
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ShiftController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $eventSearchForm = new EventSearchForm($this->getEntityManager(), $this->getLanguage());
        $unitSearchForm = new UnitSearchForm($this->getEntityManager());

        $myShifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($this->getAuthentication()->getPersonObject());

        $searchResults = null;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['event'])) {
                $eventSearchForm->setData($formData);

                if ($eventSearchForm->isValid() && '' != $formData['event']) {
                    $event = $this->getEntityManager()
                        ->getRepository('CalendarBundle\Entity\Nodes\Event')
                        ->findOneById($formData['event']);

                    $searchResults = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Shift')
                        ->findAllActiveByEvent($event);
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The given search query was invalid!'
                        )
                    );
                }
            }

            if (isset($formData['unit'])) {
                $unitSearchForm->setData($formData);

                if ($unitSearchForm->isValid() && '' != $formData['unit']) {
                    $unit = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Unit')
                        ->findOneById($formData['unit']);

                    $searchResults = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Shift')
                        ->findAllActiveByUnit($unit);
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The given search query was invalid!'
                        )
                    );
                }
            }
        }

        if (null !== $searchResults) {
            foreach ($myShifts as $shift) {
                if (in_array($shift, $searchResults))
                    unset($searchResults[array_keys($searchResults, $shift)[0]]);
            }
        }

        return new ViewModel(
            array(
                'eventSearchForm' => $eventSearchForm,
                'unitSearchForm' => $unitSearchForm,
                'myShifts' => $myShifts,
                'searchResults' => $searchResults,
                'entityManager' => $this->getEntityManager(),
                'academicYear' => $this->getCurrentAcademicYear()
            )
        );
    }

    public function responsibleAction()
    {
        $this->initAjax();

        if (!($shift = $this->_getShift()) || !($person = $this->_getPerson())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        if (!($shift->canHaveAsResponsible($this->getEntityManager(), $this->getCurrentAcademicYear(), $person))) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        $shift->addResponsible(
            $this->getEntityManager(),
            $this->getCurrentAcademicYear(),
            new Responsible(
                $person,
                $this->getCurrentAcademicYear()
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio' => $shift->countResponsibles() / $shift->getNbResponsibles()
                )
            )
        );
    }

    public function volunteerAction()
    {
        $this->initAjax();

        if (!($shift = $this->_getShift()) || !($person = $this->_getPerson())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        if (!($shift->canHaveAsVolunteer($this->getEntityManager(), $this->getCurrentAcademicYear(), $person))) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        if ($shift->countVolunteers() >= $shift->getNbVolunteers()) {
            foreach ($shift->getVolunteers() as $volunteer) {
                if ($volunteer->getPerson()->getOrganizationStatus($this->getCurrentAcademicYear()) == OrganizationStatus::$possibleStatuses['praesidium']) {
                    $shift->removeVolunteer($volunteer);

                    // @TODO: Send mail

                    $this->getEntityManager()->remove($volunteer);
                }
            }
        }

        $shift->addVolunteer(
            $this->getEntityManager(),
            $this->getCurrentAcademicYear(),
            new Volunteer(
                $person
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio' => $shift->countVolunteers() / $shift->getNbVolunteers()
                )
            )
        );
    }

    public function signoutAction()
    {
        $this->initAjax();

        if (!($shift = $this->_getShift()) || !($person = $this->_getPerson())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        if (!($shift->canSignout($this->getEntityManager(), $person))) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        $remove = $shift->removePerson($person);

        if (null !== $remove)
            $this->getEntityManager()->remove($remove);

        /**
         * @TODO If a responsible signs out, and there's another praesidium member signed up as a volunteer, promote him
         */

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio' => $shift->countVolunteers() / $shift->getNbVolunteers()
                )
            )
        );
    }

    private function _getShift()
    {
        if (null === $this->getRequest()->getPost('id'))
            return null;

        $shift = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getRequest()->getPost('id'));

        return $shift;
    }

    private function _getPerson()
    {
        if (null === $this->getRequest()->getPost('person'))
            return null;

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Person')
            ->findOneById($this->getRequest()->getPost('person'));

        return $person;
    }
}