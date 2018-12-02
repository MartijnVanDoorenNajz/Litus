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

namespace TicketBundle\Form\Ticket;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person,
    LogicException,
    RuntimeException,
    TicketBundle\Entity\Event;
use TicketBundle\Entity\Category;

/**
 * Book Tickets
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Book extends \CommonBundle\Component\Form\Bootstrap\Form
{
    private $guest_template = array(
        'type'      => 'fieldset',
        'name'      => 'guest_form_',
        'label'     => ' ',
        'elements'  => array(
            array(
                'type'  => 'text',
                'name'  => 'r-number',
                'label' => 'R-number',
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
            array(
                'type'      => 'select',
                'name'      => 'options_select',
                'label'     => 'Option',
                'attributes'=> array(),
            ),
        ),
    );

    const DEFAULT_CATEGORY = 'non_member';

    protected $hydrator = 'TicketBundle\Hydrator\Order';

    /**
     * @var Event
     */
    private $event;

    /**
     * @var Person
     */
    private $person;

    /**
     * @var AcademicYear
     */
    private $currentYear;

    public function init()
    {
        if (null === $this->event) {
            throw new LogicException('Cannot book ticket for null form.');
        }
        if (null === $this->person) {
            throw new RuntimeException('You have to be logged in to book tickets.');
        }

        parent::init();

        $this->setAttribute('id', 'ticket_sale_form');

        $status = $this->person->getOrganizationStatus($this->currentYear);
        $bookerCategory = $this->getCategory($status);
        if (null === $bookerCategory) {
            throw new RuntimeException('This category cannot book tickets.');
        }

        $this->add(array(
           'type'       => 'fieldset',
           'name'       => 'bookers_form',
           'label'      => 'Your ticket',
           'elements'   => array(
               array(
                   'type'      => 'select',
                   'name'      => 'options_select',
                   'label'     => 'Option',
                   'required'  => true,
                   'attributes'=> array(
                       'options' => $this->createOptionsArray($bookerCategory),
                   ),
               ),
           ),
        ));

        $max_nb_guests = $bookerCategory->getMaxAmountGuests();

        $this->add(array(
            'type'      => 'fieldset',
            'name'      => 'guest_form',
            'label'     => 'Guest tickets',
            'elements'  => $this->getGuestArray($max_nb_guests),
        ));

        $this->addSubmit('Book', 'book_tickets');
    }

    private function getCategory($status) {
        if ($status == null) {
            return null;
        }

        $status_string = $status->getStatus();

        foreach($this->event->getBookingCategories() as $category) {
            if ($category->getCategory() == $status_string) {
                return $category;
            }
        }
        return null;
    }

    private function createOptionsArray($category) {
        if ($category == null) {
            $category = $this->event->getBookingCategories()[0];
        }

        $options = array();
        foreach ($category->getOptions() as $option) {
            $options[] = $option->getName();
        }
        return $options;
    }

    private function getGuestArray($amount) {
        if ($amount <= 0) {
            return array();
        }

        $array = array();

        $options = $this->createOptionsArray(null);
        for ($i = 0; $i < $amount; ++$i) {
            $field = $this->guest_template;
            $field['name'] .= $i;
            $field['elements'][1]['attributes']['options'] = $options;
            $array[] = $field;
        }

        $array[0]['label'] = '';

        return $array;
    }

    /**
     * @param  Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @param AcademicYear $year
     * @return self
     */
    public function setCurrentYear(AcademicYear $year) {
        $this->currentYear = $year;

        return $this;
    }
}
