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

namespace CommonBundle\Entity\User;

use CommonBundle\Entity\User\Barcode\Ean12;
use CommonBundle\Entity\User\Barcode\Qr;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a user's barcode.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Barcode")
 * @ORM\Table(name="users_barcodes")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "ean12"="CommonBundle\Entity\User\Barcode\Ean12",
 *      "qr"="CommonBundle\Entity\User\Barcode\Qr"
 * })
 */
abstract class Barcode
{
    /**
     * @static
     * @var array All the possible status values allowed
     */
    public static $possibleTypes = array(
        'ean12' => 'EAN-12',
        'qr'    => 'QR',
    );

    /**
     * @var integer The ID of this credential
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The person associated with this barcode
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person", inversedBy="barcodes")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var DateTime The time of creation
     *
     * @ORM\Column(type="datetime")
     */
    private $creationTime;

    /**
     * Constructs a new barcode
     *
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
        $this->creationTime = new DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return integer
     */
    abstract public function getBarcode();

    /**
     * @return string|null
     */
    public function getType()
    {
        if ($this instanceof Ean12) {
            return 'ean12';
        }

        if ($this instanceof Qr) {
            return 'qr';
        }
    }
}
