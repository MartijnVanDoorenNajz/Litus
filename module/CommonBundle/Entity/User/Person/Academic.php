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

namespace CommonBundle\Entity\User\Person;

use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use CommonBundle\Entity\General\Address;
use CommonBundle\Entity\User\Status\University as UniversityStatus;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an academic person, e.g. a student or professor.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person\Academic")
 * @ORM\Table(name="users_people_academic")
 */
class Academic extends \CommonBundle\Entity\User\Person
{
    /**
     * @var string The user's personal email
     *
     * @ORM\Column(name="personal_email", type="string", length=100, nullable=true)
     */
    private $personalEmail;

    /**
     * @var string The user's university email
     *
     * @ORM\Column(name="university_email", type="string", length=100, nullable=true)
     */
    private $universityEmail;

    /**
     * @var string The user's university identification
     *
     * @ORM\Column(name="university_identification", type="string", length=8, nullable=true)
     */
    private $universityIdentification;

    /**
     * @var DateTime|null The user's birthday
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string The path to the user's photo
     *
     * @ORM\Column(name="photo_path", type="string", nullable=true)
     */
    private $photoPath;

    /**
     * @var boolean Is user an international
     *
     * @ORM\Column(name="is_international", type="boolean", options={"default" = false}, nullable=true)
     */
    private $isInternational;

    /**
     * @var boolean Is user in a working group
     *
     * @ORM\Column(name="is_in_workinggroup", type="boolean", options={"default" = false}, nullable=true)
     */
    private $isInWorkingGroup;

    /**
     * @var Address The primary address of the academic
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="primary_address", referencedColumnName="id")
     */
    private $primaryAddress;

    /**
     * @var Address The secondary address of the academic
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="secondary_address", referencedColumnName="id")
     */
    private $secondaryAddress;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The user's university statuses
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Status\University", mappedBy="person", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $universityStatuses;

    /**
     * @var ArrayCollection The user's organization mapping
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Person\Organization\AcademicYearMap", mappedBy="academic", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $organizationMap;

    /**
     * @var ArrayCollection The user's unit mapping
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Person\Organization\UnitMap\Academic", mappedBy="academic", cascade={"persist", "remove"})
     */
    private $unitMap;

    public function __construct()
    {
        parent::__construct();

        $this->universityStatuses = new ArrayCollection();
        $this->organizationMap = new ArrayCollection();
        $this->unitMap = new ArrayCollection();
    }

    /**
     * @param  string $personalEmail
     * @return self
     */
    public function setPersonalEmail($personalEmail)
    {
        $this->personalEmail = $personalEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getPersonalEmail()
    {
        return $this->personalEmail;
    }

    /**
     * @param  string $universityEmail
     * @return self
     */
    public function setUniversityEmail($universityEmail)
    {
        $this->universityEmail = $universityEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getUniversityEmail()
    {
        return $this->universityEmail;
    }

    /**
     * @param  string $universityIdentification
     * @return self
     */
    public function setUniversityIdentification($universityIdentification)
    {
        $this->universityIdentification = $universityIdentification;

        return $this;
    }

    /**
     * @return string
     */
    public function getUniversityIdentification()
    {
        return $this->universityIdentification;
    }

    /**
     * @param  string $photoPath
     * @return self
     */
    public function setPhotoPath($photoPath)
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhotoPath()
    {
        return $this->photoPath;
    }

    /**
     * @param  boolean $isInternational
     * @return self
     */
    public function setIsInternational($isInternational)
    {
        $this->isInternational = $isInternational;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInternational()
    {
        return $this->isInternational;
    }

    /**
     * @param  boolean $isInWorkingGroup
     * @return self
     */
    public function setIsInWorkingGroup($isInWorkingGroup)
    {
        $this->isInWorkingGroup = $isInWorkingGroup;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInWorkingGroup()
    {
        return $this->isInWorkingGroup;
    }

    /**
     * @param  UniversityStatus $universityStatus
     * @return self
     */
    public function addUniversityStatus(UniversityStatus $universityStatus)
    {
        $this->universityStatuses->add($universityStatus);

        return $this;
    }

    /**
     * @param  UniversityStatus $universityStatus
     * @return self
     */
    public function removeUniversityStatus(UniversityStatus $universityStatus)
    {
        $this->universityStatuses->removeElement($universityStatus);

        return $this;
    }

    /**
     * @param  AcademicYearEntity $academicYear
     * @return UniversityStatus|null
     */
    public function getUniversityStatus(AcademicYearEntity $academicYear)
    {
        foreach ($this->universityStatuses as $status) {
            if ($status->getAcademicYear() == $academicYear) {
                return $status;
            }
        }

        return null;
    }

    /**
     * @param  AcademicYearEntity $academicYear
     * @return boolean
     */
    public function canHaveUniversityStatus(AcademicYearEntity $academicYear)
    {
        if ($this->universityStatuses->count() >= 1) {
            if ($this->universityStatuses->exists(
                function ($key, $value) use ($academicYear) {
                    if ($value->getAcademicYear() == $academicYear) {
                        return true;
                    }
                }
            )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  DateTime|null $birthday
     * @return self
     */
    public function setBirthday(DateTime $birthday = null)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param  Address $primaryAddress
     * @return self
     */
    public function setPrimaryAddress(Address $primaryAddress)
    {
        $this->primaryAddress = $primaryAddress;
        $this->setAddress($primaryAddress);

        return $this;
    }

    /**
     * @return Address
     */
    public function getPrimaryAddress()
    {
        return $this->primaryAddress;
    }

    /**
     * @param  Address $secondaryAddress
     * @return self
     */
    public function setSecondaryAddress(Address $secondaryAddress)
    {
        $this->secondaryAddress = $secondaryAddress;

        return $this;
    }

    /**
     * @return Address
     */
    public function getSecondaryAddress()
    {
        return $this->secondaryAddress;
    }

    /**
     * @param  AcademicYearEntity $academicYear
     * @return \CommonBundle\Entity\General\Organization
     */
    public function getOrganization(AcademicYearEntity $academicYear)
    {
        foreach ($this->organizationMap as $map) {
            if ($map->getAcademicYear() == $academicYear) {
                return $map->getOrganization();
            }
        }

        return null;
    }

    /**
     * @param  AcademicYearEntity $academicYear
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function getUnit(AcademicYearEntity $academicYear)
    {
        foreach ($this->unitMap as $map) {
            if ($map->getAcademicYear() == $academicYear) {
                return $map->getUnit();
            }
        }

        return null;
    }

    /**
     * @param  boolean $mergeUnitRoles
     * @return array
     */
    public function getRoles($mergeUnitRoles = true)
    {
        return array_merge(
            parent::getRoles(),
            $mergeUnitRoles === true ? $this->getUnitRoles() : array()
        );
    }

    /**
     * Retrieves all the roles from the academic's units for the
     * latest and upcomming academic years.
     *
     * @return array
     */
    public function getUnitRoles()
    {
        $now = new DateTime();

        $unitMaps = array();
        foreach ($this->unitMap as $map) {
            if ($map->getAcademicYear()->getEndDate() < $now) {
                continue;
            }

            $unitMaps[] = $map;
        }

        $roles = array();
        foreach ($unitMaps as $unitMap) {
            $roles = array_merge(
                $roles,
                $unitMap->getUnit()->getRoles(),
                $unitMap->isCoordinator() ? $unitMap->getUnit()->getCoordinatorRoles() : array()
            );
        }

        return $roles;
    }
}
