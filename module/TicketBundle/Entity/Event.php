<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 10/3/18
 * Time: 12:50 PM
 */

namespace TicketBundle\Entity;

use CalendarBundle\Entity\Node\Event as CalendarEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Event")
 * @ORM\Table(name="tickets.events")
 */
class Event
{

    /**
     * @var integer The ID of the event
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var boolean Flag whether the event booking system is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var CalendarEvent The activity of the event
     *
     * @ORM\OneToOne(targetEntity="CalendarBundle\Entity\Node\Event")
     * @ORM\JoinColumn(name="activity", referencedColumnName="id")
     */
    private $activity;

    /**
     * @var ArrayCollection The categories that can book tickets
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Category", mappedBy="event", cascade={"remove"})
     * @ORM\JoinColumn(name="booking_category", referencedColumnName="id")
     */
    private $bookingCategories;

    /**
     * @var boolean Flag whether the reservation can be removed by the user.
     *
     * @ORM\Column(type="boolean")
     */
    private $allowRemove;

    /**
     * @var integer The maximum number of tickets available. If negative, look inside the
     * bookingCategories. Zero means an infinite amount.
     *
     * @ORM\Column(type="integer")
     */
    private $maxNumberTickets;

    /**
     * @var ArrayCollection The orders for this event.
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\OrderEntity", mappedBy="event")
     */
    private $orders;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return CalendarEvent
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param CalendarEvent $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return ArrayCollection
     */
    public function getBookingCategories()
    {
        return $this->bookingCategories;
    }

    /**
     * @param ArrayCollection $bookingCategories
     */
    public function setBookingCategories($bookingCategories)
    {
        $this->bookingCategories = $bookingCategories;
    }

    public function addBookingCategory($bookingCategory)
    {
        $this->bookingCategories[] = $bookingCategory;
    }

    public function getBookingCategoryByStatus($status)
    {
        if ($status == null) {
            return null;
        }

        $status_string = $status->getStatus();

        foreach ($this->getBookingCategories() as $category) {
            if ($category->getCategory() == $status_string) {
                return $category;
            }
        }
        return null;
    }

    /**
     * @return boolean
     */
    public function isAllowRemove()
    {
        return $this->allowRemove;
    }

    /**
     * @param boolean $allowRemove
     */
    public function setAllowRemove($allowRemove)
    {
        $this->allowRemove = $allowRemove;
    }

    /**
     * @return integer
     */
    public function getMaxNumberTickets()
    {
        return $this->maxNumberTickets;
    }

    /**
     * @param integer $maxNumberTickets
     */
    public function setMaxNumberTickets($maxNumberTickets)
    {
        $this->maxNumberTickets = $maxNumberTickets;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ArrayCollection $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @param OrganisationStatus $status
     */
    public function canBookTickets($status)
    {
        $now = new DateTime();
        $category = $this->getBookingCategoryByStatus($status);
        if ($category == null) {
            return false;
        }
        return ($category->getBookingOpenDate() <= $now) and ($category->getBookingCloseDate() >= $now);
    }
}
