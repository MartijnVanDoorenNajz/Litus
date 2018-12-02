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

namespace TicketBundle\Hydrator;

use TicketBundle\Entity\Category as CategoryEntity,
    TicketBundle\Entity\Event as EventEntity;
use TicketBundle\Entity\Option as OptionEntity;
use TicketBundle\Form\Admin\Event\Add;

class Event extends \CommonBundle\Component\Hydrator\Hydrator
{

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new EventEntity();
        }

        $calendarEvent = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneById($data['event']);


        $object->setActivity($calendarEvent);

        // Get old id's
        $cat_ids = array();
        $option_ids = array();

        $old_booking_cats = $object->getBookingCategories();
        foreach(($old_booking_cats ? $old_booking_cats : array()) as $category) {
            $cat_ids[$category->getId()] = $category;
            $old_options = $category->getOptions();
            foreach(($old_options ? $old_options : array()) as $option) {
                $option_ids[$option->getId()] = $option;
            }
        }

        // Generate all categories
        $categories = array();
        foreach($data['bookableCategories'] as $category) {
            // Check if there was already a category object connected to this field
            if (isset($data['category_ids']['category_id_'.$category]) &&
                is_numeric($data['category_ids']['category_id_'.$category])) {
                $catEntity = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Category')
                        ->findOneById($data['category_ids']['category_id_'.$category]);
                unset($cat_ids[$catEntity->getId()]);
            } else {
                $catEntity = new CategoryEntity();
                $catEntity->setEvent($object);
                $catEntity->setCategory($category);

                $this->getEntityManager()->persist($catEntity);
            }
            $object->addBookingCategory($catEntity);
            $categories[$category] = $catEntity;
        }

        // Delete unused categories
        foreach($cat_ids as $category) {
            $this->getEntityManager()->remove($category);
        }


        // Get dates for all categories
        if ($data['bookings']['sameOpenDate']) {
            $date = $data['bookings']['booking_open_date'];
            foreach($categories as $category) {
                $category->setBookingOpenDate(self::loadDateTime($date));
            }
        } else {
            foreach($categories as $key => $category) {
                $category->setBookingOpenDate(self::loadDateTime($data['bookings']['booking_open_date_'.$key]));
            }
        }

        if ($data['bookings']['sameCloseDate']) {
            $date = $data['bookings']['booking_close_date'];
            foreach($categories as $category) {
                $category->setBookingCloseDate(self::loadDateTime($date));
            }
        } else {
            foreach($categories as $key => $category) {
                $category->setBookingCloseDate(self::loadDateTime($data['bookings']['booking_close_date_'.$key]));
            }
        }

        // Get maximum number of tickets for all categories
        if ($data['tickets']['totalAcrossAll']) {
            $object->setMaxNumberTickets($data['tickets']['max_nb_tickets']);
            foreach($categories as $category) {
                $category->setMaxNumberTickets(-1);
            }
        } else {
            $object->setMaxNumberTickets(-1);
            foreach($categories as $key => $category) {
                $category->setMaxNumberTickets($data['tickets']['max_nb_tickets_'.$key]);
            }
        }

        // Get maximum amount of guests for all categories
        if ($data['tickets']['sameAmountGuests']) {
            $amount = $data['tickets']['nb_guests'];
            foreach($categories as $category) {
                $category->setMaxAmountGuests($amount);
            }
        } else {
            foreach($categories as $key => $category) {
                $category->setMaxAmountGuests($data['tickets']['nb_guests_'.$key]);
            }
        }

        // Get all options for all categories (retrieve from database)
        if ($data['prices_form']['enable_options']) {
            foreach($data['prices_options_form'] as $optionData) {
                if (strlen($optionData['name']) == 0) {
                    continue;
                }

                $same_price = $optionData['same_price'];
                foreach($categories as $key => $category) {
                    if (isset($optionData['option_ids']['option_id_'.$key]) && is_numeric($optionData['option_ids']['option_id_'.$key])) {
                        $option = $this->getEntityManager()
                            ->getRepository('TicketBundle\Entity\Option')
                            ->findOneById($optionData['option_ids']['option_id_'.$key]);
                        unset($option_ids[$option->getId()]);
                    } else {
                        $option = new OptionEntity();
                        $option->setCategory($category);

                        $this->getEntityManager()->persist($option);
                    }
                    $option->setName($optionData['name']);
                    if ($same_price) {
                        $option->setPrice($optionData['price']);
                    } else {
                        $option->setPrice($optionData['price_'.$key]);
                    }
                }
            }
        } else {
            foreach ($categories as $key => $category) {
                if (isset($data['prices_single_form']['prices_ids']['prices_id_'.$key]) && is_numeric($data['prices_single_form']['prices_ids']['prices_id_'.$key])) {
                    $option = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Option')
                        ->findOneById($data['prices_single_form']['prices_ids']['prices_id_'.$key]);
                    unset($option_ids[$option->getId()]);
                } else {
                    $option = new OptionEntity();
                    $option->setCategory($category);
                    $category->addOption($option);

                    $this->getEntityManager()->persist($option);
                }

                $option->setName(Add::DEFAULT_OPTION_NAME);

                if ($data['prices_single_form']['same_price']) {
                    $option->setPrice($data['prices_single_form']['price']);
                } else {
                    $option->setPrice($data['prices_single_form']['price_'.$key]);
                }
            }

        }

        // Remove all unused options
        foreach($option_ids as $id => $option) {
            $this->getEntityManager()->remove($option);
        }


        // Set allowRemove option
        $object->setAllowRemove($data['allow_remove']);


        return $object;
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = array();

        // Extract event
        $data['event'] = $object->getActivity()->getId();

        // Extract category id's
        $categories = array();
        foreach($object->getBookingCategories() as $category) {
            $categories[$category->getCategory()] = $category;
            $data['category_ids']['category_id_'.$category->getCategory()] = $category->getId();
        }

        // Set correct selected categories
        $data['bookableCategories'] = array_keys($categories);

        // Set allow removal checkbox
        $data['allow_remove'] = $object->isAllowRemove();

        // Set booking dates (with corresponding checkboxes)
        $booking_open_dates = array();
        $open_reference = null;
        $same_open_date = true;
        $booking_close_dates = array();
        $close_reference = null;
        $same_close_date = true;
        $ref_set = false;
        foreach($categories as $key => $category) {
            $booking_open_dates[$key] = $category->getBookingOpenDate();
            $booking_close_dates[$key] = $category->getBookingCloseDate();

            if ($ref_set) {
                if ($same_open_date && $open_reference != $booking_open_dates[$key]) {
                    $same_open_date = false;
                }
                if ($same_close_date && $close_reference != $booking_close_dates[$key]) {
                    $same_close_date = false;
                }
            } else {
                $ref_set = true;
                $open_reference = $booking_open_dates[$key];
                $close_reference = $booking_close_dates[$key];
            }
        }

        if ($same_open_date) {
            $data['bookings']['sameOpenDate'] = true;
            $data['bookings']['booking_open_date'] = $open_reference->format('d/m/Y H:i');
        } else {
            $data['bookings']['sameOpenDate'] = false;
            foreach($booking_open_dates as $key => $booking_open_date) {
                $data['bookings']['booking_open_date_'.$key] = $booking_open_date->format('d/m/Y H:i');
            }
        }

        if ($same_close_date) {
            $data['bookings']['sameCloseDate'] = true;
            $data['bookings']['booking_close_date'] = $close_reference->format('d/m/Y H:i');
        } else {
            $data['bookings']['sameCloseDate'] = false;
            foreach($booking_close_dates as $key => $booking_close_date) {
                $data['bookings']['booking_close_date_'.$key] = $booking_close_date->format('d/m/Y H:i');
            }
        }

        // Set maximum amount of tickets (with corresponding checkbox)
        $max_nb_tickets = $object->getMaxNumberTickets();
        if ($max_nb_tickets != -1) {
            $data['tickets']['totalAcrossAll'] = true;
            $data['tickets']['max_nb_tickets'] = $max_nb_tickets;
        } else {
            $data['tickets']['totalAcrossAll'] = false;
            foreach($categories as $key => $category) {
                $data['tickets']['max_nb_tickets_'.$key] = $category->getMaxNumberTickets();
            }
        }

        // Set maximum amount of guests (with corresponding checkbox)
        $nb_guests = array();
        $same_nb_guests = true;
        $guest_reference = null;
        $ref_set = false;
        foreach($categories as $key => $category) {
            $nb_guests[$key] = $category->getMaxAmountGuests();
            if ($ref_set) {
                if ($same_nb_guests && $guest_reference != $nb_guests[$key]) {
                    $same_nb_guests = false;
                }
            } else {
                $ref_set = true;
                $guest_reference = $nb_guests[$key];
            }
        }

        if ($same_nb_guests) {
            $data['tickets']['sameAmountGuests'] = true;
            $data['tickets']['nb_guests'] = $guest_reference;
        } else {
            $data['tickets']['sameAmountGuests'] = false;
            foreach($nb_guests as $key => $max_guests) {
                $data['tickets']['nb_guests_'.$key] = $max_guests;
            }
        }

        // Set the prices
        // Extract all the options
        $options = array();
        foreach($categories as $key => $category) {
            foreach($category->getOptions() as $option) {
                $options[$option->getName()][$key] = $option;
            }
        }

        // Check if options were enabled
        $enable_options = !isset($options[Add::DEFAULT_OPTION_NAME]);
        $data['prices_form']['enable_options'] = $enable_options;

        // Extract all prices into the correct fields
        foreach($options as $key => $option_cat) {
            $optionData = array();

            // Extract the prices for this option of all categories
            $prices = array();
            $same_price = true;
            $price_reference = null;
            $ref_set = false;
            foreach($option_cat as $cat_name => $option) {
                $prices[$cat_name] = $option->getPrice() * 100;
                $optionData['prices_ids']['prices_id_'.$cat_name] = $option->getId();
                if ($ref_set) {
                    if ($same_price && $price_reference != $prices[$cat_name]) {
                        $same_price = false;
                    }
                } else {
                    $ref_set = true;
                    $price_reference = $prices[$cat_name];
                }
            }

            // Check if the prices are all the same
            if ($same_price) {
                $optionData['same_price'] = true;
                $optionData['price'] = $price_reference;
            } else {
                $optionData['same_price'] = false;
                foreach($prices as $cat_name => $price) {
                    $optionData['price_'.$cat_name] = $price;
                }
            }

            // Check if options were enabled
            if ($enable_options) {
                $optionData['name'] = $key;
                $data['prices_options_form'][] = $optionData;
            } else {
                $data['prices_single_form'] = $optionData;
            }
        }


        return $data;
    }
}
