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

namespace TicketBundle\Controller;

use Zend\View\Model\ViewModel;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\Ticket;


/**
 * TicketController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TicketController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function eventAction()
    {

        if (!($event = $this->getEventEntity())) {
            return $this->notFoundAction();
        }

        if (!($person = $this->getPersonEntity())) {
            return $this->notFoundAction();
        }

        $tickets = $this->getEntityManager()
           ->getRepository('TicketBundle\Entity\Ticket')
           ->findAllByEventAndPerson($event, $person);

        $currentYear = $this->getCurrentAcademicYear();

        $form = $this->getForm('ticket_ticket_book', array('event' => $event, 'person' => $person, 'currentYear' => $currentYear));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            $hydrator = $form->getHydrator();
            $hydrator->setEvent($event);
            $hydrator->setPerson($person);
            $hydrator->setCurrentYear($currentYear);

            if ($form->isValid()) {
                if ($order = $form->hydrateObject()) {
                    $this->getEntityManager()->persist($order);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The tickets were succesfully booked'
                    );

                    $this->redirect()->toRoute(
                        'ticket',
                        array(
                            'action' => 'event',
                            'id'     => $event->getId(),
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'form'   => $form,
                'event'  => $event,
                'person' => $person,
                'currentYear' => $currentYear,
                'tickets' => $tickets,
                'entityManager' => $this->getEntityManager(),
            )
        );

//        $tickets = $this->getEntityManager()
//            ->getRepository('TicketBundle\Entity\Ticket')
//            ->findAllByEventAndPerson($event, $person);
//
//        $form = $this->getForm('ticket_ticket_book', array('event' => $event, 'person' => $person));
//
//        if ($this->getRequest()->isPost()) {
//            $form->setData($this->getRequest()->getPost());
//
//            if ($form->isValid()) {
//                $formData = $form->getData();
//
//                $numbers = array(
//                    'member'     => isset($formData['number_member']) ? $formData['number_member'] : 0,
//                    'non_member' => isset($formData['number_non_member']) ? $formData['number_non_member'] : 0,
//                );
//
//                foreach ($event->getOptions() as $option) {
//                    $numbers['option_' . $option->getId() . '_number_member'] = $formData['option_' . $option->getId() . '_number_member'];
//                    $numbers['option_' . $option->getId() . '_number_non_member'] = $formData['option_' . $option->getId() . '_number_non_member'];
//                }
//
//                TicketBook::book(
//                    $event,
//                    $numbers,
//                    false,
//                    $this->getEntityManager(),
//                    $person,
//                    null
//                );
//
//                $this->getEntityManager()->flush();
//
//                $this->flashMessenger()->success(
//                    'Success',
//                    'The tickets were succesfully booked'
//                );
//
//                $this->redirect()->toRoute(
//                    'ticket',
//                    array(
//                        'action' => 'event',
//                        'id'     => $event->getId(),
//                    )
//                );
//            }
//        }
//
//        $organizationStatus = $person->getOrganizationStatus($this->getCurrentAcademicYear());
//
//        return new ViewModel(
//            array(
//                'event'                 => $event,
//                'tickets'               => $tickets,
//                'form'                  => $form,
//                'canRemoveReservations' => $event->canRemoveReservation($this->getEntityManager()),
//                'isPraesidium'          => $organizationStatus ? $organizationStatus->getStatus() == 'praesidium' : false,
//            )
//        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($ticket = $this->getTicketEntity())) {
            return $this->notFoundAction();
        }

        if ($ticket->getEvent()->areTicketsGenerated()) {
            $ticket->setStatus('annulled');
        } else {
            $this->getEntityManager()->remove($ticket);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('TicketBundle\Entity\Event');

        if (!($event instanceof Event) || !$event->isActive()) {
            return null;
        }

        return $event;
    }

    /**
     * @return Ticket|null
     */
    private function getTicketEntity()
    {
        if (!($person = $this->getPersonEntity())) {
            return null;
        }

        $ticket = $this->getEntityById('TicketBundle\Entity\Ticket');

        if (!($ticket instanceof Ticket) || $ticket->getPerson() != $person) {
            return null;
        }

        return $ticket;
    }
}
