<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 10/4/18
 * Time: 2:23 PM
 */

namespace TicketBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Ticket")
 * @ORM\Table(name="tickets.tickets")
 */
class Ticket
{

    /**
     * @var array The possible states of a ticket
     */
    const POSSIBLE_STATUSES = array(
        'annulled'  => 'Annulled',
        'booked'    => 'Booked',
        'sold'      => 'Sold',
    );

    /**
     * @var integer The ID of the ticket
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The status of the ticket, see above
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var Order The order that the ticket is part of
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\OrderEntity", inversedBy="tickets")
     * @ORM\JoinColumn(name="orderEntity", referencedColumnName="id")
     */
    private $orderEntity;

    /**
     * @var Person|null The person who bought/reserved the order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var DateTime|null The date the ticket was booked
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $bookDate;

    /**
     * @var DateTime|null The date the ticket was sold
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $soldDate;

    /**
     * @var Option The option of the ticket
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Option")
     * @ORM\JoinColumn(name="option", referencedColumnName="id")
     */
    private $option;

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
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return OrderEntity
     */
    public function getOrderEntity()
    {
        return $this->orderEntity;
    }

    /**
     * @param OrderEntity $orderEntity
     */
    public function setOrderEntity($orderEntity)
    {
        $this->orderEntity = $orderEntity;
    }

    /**
     * @return Person|null
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person|null $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return DateTime|null
     */
    public function getBookDate()
    {
        return $this->bookDate;
    }

    /**
     * @param DateTime|null $bookDate
     */
    public function setBookDate($bookDate)
    {
        $this->bookDate = $bookDate;
    }

    /**
     * @return DateTime|null
     */
    public function getSoldDate()
    {
        return $this->soldDate;
    }

    /**
     * @param DateTime|null $soldDate
     */
    public function setSoldDate($soldDate)
    {
        $this->soldDate = $soldDate;
    }

    /**
     * @return Option
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param Option $option
     */
    public function setOption($option)
    {
        $this->option = $option;
    }
}
