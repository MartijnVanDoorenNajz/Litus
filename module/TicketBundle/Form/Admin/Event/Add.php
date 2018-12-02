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

namespace TicketBundle\Form\Admin\Event;

use Ticketbundle\Entity\Event,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus;

/**
 * Add Event
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{

    //TODO: Add validators to the form!

    const DEFAULT_OPTION_NAME = "default";

    protected $hydrator = 'TicketBundle\Hydrator\Event';

    protected $event;

    public function init()
    {
        parent::init();

        $this->setAttribute('class', $this->getAttribute('class') . ' half_width');

        $this->add(array(
            'type'       => 'select',
            'name'       => 'event',
            'label'      => 'Event',
            'required'   => true,
            'attributes' => array(
                'options' => $this->createEventsArray(),
            ),
            'options' => array(
                'input' => array(
                    'filter' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'ticket_activtiy'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'      => 'fieldset',
            'name'      => 'category_ids',
            'elements'  => Add::copyForAllCategories(array(
                'type'     => 'hidden',
                'name'     => 'category_id',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'int',
                            ),
                        ),
                    ),
                ),
            ))
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'bookableCategories',
            'label'      => 'Bookable by',
            'required'   => true,
            'attributes' => array(
                'multiple'  => true,
                'value'     => array_keys(Add::getBookableCategories()),
                'options'   => Add::getBookableCategories(),
                'data-help' => '',
                'id'        => 'bookable_categories',
            ),
        ));

        $this->add(array(
            'type'     => 'checkbox',
            'name'     => 'allow_remove',
            'label'    => 'Allow Removal',
            'required' => false,
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'bookings',
            'label'      => 'Bookings',
            'attributes' => array(
                'id' => 'bookings_form',
            ),
            'elements' => array_merge(array_merge(array(
                array(
                    'type'     => 'checkbox',
                    'name'     => 'sameOpenDate',
                    'label'    => 'Same Open Date',
                    'required' => false,
                    'attributes' => array(
                        'id'    => 'sameOpenDate',
                        'value' => true,
                    ),
                ),
                array(
                    'type'     => 'datetime',
                    'name'     => 'booking_open_date',
                    'label'    => 'Booking Open Date',
                    'required' => false,
                    'attributes' => array(
                        'id' => 'booking_open_date',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name'    => 'date',
                                    'options' => array(
                                        'format' => 'd/m/Y H:i',
                                    ),
                                ),
                                array(
                                    'name'    => 'date_compare',
                                    'options' => array(
                                        'first_date' => 'now',
                                        'format'     => 'd/m/Y H:i',
                                    ),
                                ),
//                                array(
//                                    'name'    => 'ticket_date',
//                                    'options' => array(
//                                        'format' => 'd/m/Y H:i',
//                                    ),
//                                ),
                            ),
                        ),
                    ),
                )),
                Add::copyForAllCategories(array(
                    'type'     => 'datetime',
                    'name'     => 'booking_open_date',
                    'label'    => 'Booking Open Date',
                    'required' => false,
                    'attributes' => array(
                        'id' => "booking_open_date",
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name'    => 'date',
                                    'options' => array(
                                        'format' => 'd/m/Y H:i',
                                    ),
                                ),
                                array(
                                    'name'    => 'date_compare',
                                    'options' => array(
                                        'first_date' => 'now',
                                        'format'     => 'd/m/Y H:i',
                                    ),
                                ),
//                                array(
//                                    'name'    => 'ticket_date',
//                                    'options' => array(
//                                        'format' => 'd/m/Y H:i',
//                                    ),
//                                ),
                            ),
                        ),
                    ),
                ))
            ), array_merge(array(
                array(
                    'type'     => 'checkbox',
                    'name'     => 'sameCloseDate',
                    'label'    => 'Same Close Date',
                    'required' => false,
                    'attributes' => array(
                        'id'    => 'sameCloseDate',
                        'value' => true,
                    ),
                ),
                array(
                    'type'     => 'datetime',
                    'name'     => 'booking_close_date',
                    'label'    => 'Booking Close Date',
                    'required' => false,
                    'attributes' => array(
                        'id' => 'booking_close_date',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name'    => 'date',
                                    'options' => array(
                                        'format' => 'd/m/Y H:i',
                                    ),
                                ),
                                array(
                                    'name'    => 'date_compare',
                                    'options' => array(
                                        'first_date' => 'now',
                                        'format'     => 'd/m/Y H:i',
                                    ),
                                ),
//                                array(
//                                    'name'    => 'ticket_date',
//                                    'options' => array(
//                                        'format' => 'd/m/Y H:i',
//                                    ),
//                                ),
                            ),
                        ),
                    ),
                )),
                Add::copyForAllCategories(array(
                    'type'     => 'datetime',
                    'name'     => 'booking_close_date',
                    'label'    => 'Booking Close Date',
                    'required' => false,
                    'attributes' => array(
                        'id' => "booking_close_date",
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name'    => 'date',
                                    'options' => array(
                                        'format' => 'd/m/Y H:i',
                                    ),
                                ),
                                array(
                                    'name'    => 'date_compare',
                                    'options' => array(
                                        'first_date' => 'now',
                                        'format'     => 'd/m/Y H:i',
                                    ),
                                ),
//                                array(
//                                    'name'    => 'ticket_date',
//                                    'options' => array(
//                                        'format' => 'd/m/Y H:i',
//                                    ),
//                                ),
                            ),
                        ),
                    ),
                ))
            ))
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'tickets',
            'label'      => 'Tickets',
            'attributes' => array(
                'id' => 'tickets_form',
            ),
            'elements' => array_merge(array_merge(array(
                array(
                    'type'     => 'checkbox',
                    'name'     => 'totalAcrossAll',
                    'label'    => 'Total across all categories',
                    'required' => false,
                    'attributes' => array(
                        'id'    => 'totalAcrossAll',
                        'value' => true,
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'max_nb_tickets',
                    'label'    => 'Max number tickets (0: no limit)',
                    'required' => false,
                    'attributes' => array(
                        'id' => 'max_nb_tickets',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )),
                Add::copyForAllCategories(array(
                    'type'     => 'text',
                    'name'     => 'max_nb_tickets',
                    'label'    => 'Max number tickets',
                    'required' => false,
                    'attributes' => array(
                        'id' => "max_nb_tickets",
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ))
            ), array_merge(array(
                array(
                    'type'     => 'checkbox',
                    'name'     => 'sameAmountGuests',
                    'label'    => 'Same amount of guests',
                    'required' => false,
                    'attributes' => array(
                        'id'    => 'sameAmountGuests',
                        'value' => true,
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'nb_guests',
                    'label'    => 'Amount of guests (-1: no limit)',
                    'required' => false,
                    'attributes' => array(
                        'id' => 'nb_guests',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )),
                Add::copyForAllCategories(array(
                    'type'     => 'text',
                    'name'     => 'nb_guests',
                    'label'    => 'Amount of guests',
                    'required' => false,
                    'attributes' => array(
                        'id' => "nb_guests",
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ))
            ))
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'prices_form',
            'label'      => 'Prices',
            'attributes' => array(
                'id' => 'prices_form',
            ),
            'elements' => array(array(
                'type'     => 'checkbox',
                'name'     => 'enable_options',
                'label'    => 'Enable options',
                'required' => false,
                'attributes' => array(
                    'id'    => 'enable_options',
                    'value' => false,
                ),
            )),
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'prices_single_form',
            'label'      => ' ',
            'attributes' => array(
                'id' => 'prices_single_form',
            ),
            'elements' => array_merge(array(
                array(
                    'type'     => 'checkbox',
                    'name'     => 'same_price',
                    'label'    => 'All same price',
                    'required' => false,
                    'attributes' => array(
                        'id'    => 'same_price',
                        'value' => true,
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'price',
                    'label'    => 'Price',
                    'required' => false,
                    'attributes' => array(
                        'id' => 'price',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'      => 'fieldset',
                    'name'      => 'prices_ids',
                    'elements'  => Add::copyForAllCategories(array(
                        'type'     => 'hidden',
                        'name'     => 'prices_id',
                        'required' => false,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name' => 'int',
                                    ),
                                ),
                            ),
                        ),
                    )),
                )),
                Add::copyForAllCategories(array(
                    'type'     => 'text',
                    'name'     => 'price',
                    'label'    => 'Price',
                    'required' => false,
                    'attributes' => array(
                        'id' => 'price',
                    ),
                    'options'  => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ))
            ),
        ));


        $this->add(array(
            'type'    => 'collection',
            'name'    => 'prices_options_form',
            'label'   => 'Options',
            'attributes' => array(
                'id' => 'prices_options_form',
            ),
            'options' => array(
                'count'                  => 0,
                'should_create_template' => true,
                'allow_add'              => true,
                'target_element'         => array(
                    'type' => 'ticket_event_option',
                ),
            ),
        ));

        if (null !== $this->event) {
            $this->bind($this->event);
        }

        $this->addSubmit('Add', 'shift_add');
    }

    public static function getBookableCategories() {
        return OrganizationStatus::$possibleStatuses;
    }

    public static function copyForAllCategories(array $arrayToCopy) {

        $result = array();

        $categories = Add::getBookableCategories();
        $categoryKeys = array_keys($categories);

        foreach ($categoryKeys as $category) {
            $newArray = $arrayToCopy;
            $categoryDisplay = $categories[$category];

            if (array_key_exists('name', $newArray)) {
                $newArray['name'] .= "_" . $category;
            } else {
                $newArray['name'] = $category;
            }

            if (array_key_exists('label', $newArray)) {
                $newArray['label'] .= " - " . $categoryDisplay;
            } else {
                $newArray['label'] = $categoryDisplay;
            }

            if (array_key_exists('attributes', $newArray) && array_key_exists('id', $newArray['attributes'])) {
                $newArray['attributes']['id'] .= "_" . $category;
            } else {
                $newArray['attributes']['id'] = $category;
            }

            $result[] = $newArray;
        }

        return $result;
    }

    protected function createEventsArray()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $eventsArray = array(
            '' => '',
        );
        foreach ($events as $event) {
            $eventsArray[$event->getId()] = $event->getTitle();
        }

        return $eventsArray;
    }

    public function setEvent(Event $event) {
        $this->event = $event;

        return $this;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

//        if (!$this->data['generate_tickets']) {
//            foreach ($specs['number_of_tickets']['validators'] as $key => $validator) {
//                if ('greaterthan' == $validator['name']) {
//                    unset($specs['number_of_tickets']['validators'][$key]);
//                }
//            }
//        }
//
//        if ((isset($this->data['enable_options']) && $this->data['enable_options']) || (isset($this->data['enable_options_hidden']) && $this->data['enable_options_hidden']) == '1') {
//            unset($specs['prices']);
//        } else {
//            $specs['prices']['price_non_members']['required'] = !(isset($this->data['only_members']) && $this->data['only_members']);
//        }

        return $specs;
    }
}
