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

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The entity for a stock article
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Article")
 * @ORM\Table(name="logistics_article")
 */
class Article
{
    /**
     * @var integer The item's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the article
     *
     * @ORM\Column(type="text")
     */
    private $name;

//    /**
//     * @var ArrayCollection An array of \LogisticsBundle\Entity\Order indicating when this article is ordered (reserved).
//     *
//     * @ORM\OneToMany(targetEntity="LogisticsBundle\Entity\Order", mappedBy="article_id")
//     */
//    private $orders;

    /**
     * @var string Additional information about the article
     *
     * @ORM\Column(name="additional_info", type="text")
     */
    private $additionalInfo;

    /**
     * @var int The amount of owned articles
     *
     * @ORM\Column(name="amount_owned", type="int")
     */
    private $amountOwned;

    /**
     * @var int The amount of available articles
     *
     * @ORM\Column(name="amount_available", type="int")
     */
    private $amountAvailable;

    /**
     * @var string The visibility of this article
     *
     * @ORM\Column(name="visibility", type="text")
     */
    private $visibility;

    /**
     * @static
     * @var array All the possible visibilities allowed
     */
    public static $possibleVisibilities = array(
        'internal' => 'Internal',
        'external' => 'External',
        'private' => 'Private',
    );

    /**
     * @var string The status of this article
     *
     * @ORM\Column(name="status", type="text")
     */
    private $status;

    /**
     * @static
     * @var array All the possible statuses allowed
     */
    public static $possibleStatuses = array(
//        TODO: vragen aan Sietze
    );

    /**
     * @var string The location of storage of this article
     *
     * @ORM\Column(name="location", type="text")
     */
    private $location;

    /**
     * @var int The warranty of this article
     *
     * @ORM\Column(name="warranty", type="int")
     */
    private $warranty;

    /**
     * @var int The rent of this article
     *
     * @ORM\Column(name="rent", type="int")
     */
    private $rent;

    /**
     * @var string The type of this article
     *
     * @ORM\Column(name="category", type="text")
     */
    private $category;

    /**
     * @static
     * @var array All the possible categories allowed
     */
    public static $possibleCategories = array(
//        TODO: vragen aan Sietze
        'varia' => 'Varia',
        'sound' => 'Sound',
    );

    /**
     * @var DateTime The last time this article was updated.
     *
     * @ORM\Column(name="date_updated", type="date")
     */
    private $dateUpdated;

    /**
     * @param string  $name     The article's name
     * @param string  $location The article's location
     */
    public function __construct($name, $location)
    {
        $this->name = $name;
        $this->location = $location;
        $this->dateUpdated = new DateTime();
        $this->orders = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    /**
     * @param  string $additionalInfo
     * @return self
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
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
    public function addOrder($orders)
    {
        $this->orders->add($orders);
    }

    /**
     * @return int
     */
    public function getAmountOwned()
    {
        return $this->amountOwned;
    }

    /**
     * @param int $amountOwned
     */
    public function setAmountOwned($amountOwned)
    {
        $this->amountOwned = $amountOwned;
    }

    /**
     * @return int
     */
    public function getAmountAvailable()
    {
        return $this->amountAvailable;
    }

    /**
     * @param int $amountAvailable
     */
    public function setAmountAvailable($amountAvailable)
    {
        $this->amountAvailable = $amountAvailable;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
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
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return int
     */
    public function getWarranty()
    {
        return $this->warranty;
    }

    /**
     * @param int $warranty
     */
    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;
    }

    /**
     * @return int
     */
    public function getRent()
    {
        return $this->rent;
    }

    /**
     * @param int $rent
     */
    public function setRent($rent)
    {
        $this->rent = $rent;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }


}