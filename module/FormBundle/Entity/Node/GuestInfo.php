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

namespace FormBundle\Entity\Node;

use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Http\PhpEnvironment\Request;

/**
 * This entity stores info about a guest.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\GuestInfo")
 * @ORM\Table(name="nodes_forms_guests_info")
 */
class GuestInfo
{
    /**
     * @var integer The ID of this node
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The session id of this guest info item
     *
     * @ORM\Column(type="string", length=32, unique=true, nullable=true)
     */
    private $sessionId;

    /**
     * @var string The first name of this guest
     *
     * @ORM\Column(name="first_name", type="string")
     */
    private $firstName;

    /**
     * @var string The last name of this guest
     *
     * @ORM\Column(name="last_name", type="string")
     */
    private $lastName;

    /**
     * @var string The email address of this guest
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    public static $cookieNamespace = 'Litus_Form';

    /**
     * @param EntityManager $entityManager
     * @param Request       $request
     */
    public function __construct(EntityManager $entityManager, Request $request)
    {
        do {
            $sessionId = md5(uniqid(rand(), true));

            $guestInfo = $entityManager->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($sessionId);
        } while ($guestInfo !== null);

        $this->sessionId = $sessionId;

        $this->renew($request);
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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string firstName
     *
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getOrganizationStatus(AcademicYearEntity $academicYear)
    {
        return '';
    }

    /**
     * @param  Request $request
     * @return \FormBundle\Entity\Node\GuestInfo
     */
    public function renew(Request $request)
    {
        setcookie(
            self::$cookieNamespace,
            $this->sessionId,
            time() + (60 * 60 * 24 * 25),
            '/',
            str_replace(array('www.', ','), '', $request->getServer('SERVER_NAME'))
        );

        return $this;
    }
}
