<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\General\Organization;

use CommonBundle\Entity\General\Organization,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a unit of the organization.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Organization\Unit")
 * @ORM\Table(name="general.organizations_units")
 */
class Unit
{
    /**
     * @var integer The ID of this unit
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The unit's name
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \CommonBundle\Entity\General\Organization The unit's organization
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="organization", referencedColumnName="id")
     */
    private $organization;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The roles associated with the unit
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="general.organizations_units_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="unit", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $roles;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The roles associated with the coordinator of the unit
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="general.organizations_units_coordinator_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="unit", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $coordinatorRoles;

    /**
     * @var boolean Whether or not this unit is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @param string $name The unit's name
     * @param \CommonBundle\Entity\General\Organization $organization The unit's organization
     * @param array $roles The roles associated with the unit
     * @param array $coordinatorRoles The roles associated with the coordinator of the unit
     */
    public function __construct($name, Organization $organization, array $roles, array $coordinatorRoles)
    {
        $this->name = $name;
        $this->organization = $organization;
        $this->active = true;

        $this->coordinatorRoles = new ArrayCollection($coordinatorRoles);
        $this->roles = new ArrayCollection($roles);
    }

    /**
     * @return string
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
     * @param string $name
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param \CommonBundle\Entity\General\Organization $organization
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * @return array
     */
    public function getCoordinatorRoles()
    {
        return $this->coordinatorRoles->toArray();
    }

    /**
     * @param array $coordinatoRoles
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setCoordinatorRoles(array $coordinatorRoles)
    {
        $this->coordinatorRoles = new ArrayCollection($coordinatorRoles);
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * @param array $roles
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function setRoles(array $roles)
    {
        $this->roles = new ArrayCollection($roles);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        $this->active = false;
    }
}
