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
namespace PromBundle\Entity\Bus;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for reservationcodes.
 *
 * @ORM\Entity(repositoryClass="PromBundle\Repository\Bus\ReservationCode")
 * @ORM\Table(name="prom.bus_code")
 */
class ReservationCode
{
    /**
     * @var int The ID of this guest info
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The code for the passenger.
     *
     * @ORM\Column(type="string", length=10)
     */
    private $code;

    /**
     * @var boolean If the code is used or not.
     *
     * @ORM\Column(name="old", type="boolean")
     */
    private $used;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $code
     */
    public function __construct()
    {
        $this->code = $this->_generateCode();
        $this->used = false;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * Sets the code as used.
     */
    public function setUsed()
    {
        $this->used = true;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    private function _generateCode()
    {
        return $this->_generateRandomString();
    }

    /**
     * @return string
     */
    private function _generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
