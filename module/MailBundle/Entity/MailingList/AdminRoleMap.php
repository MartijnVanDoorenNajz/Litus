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

namespace MailBundle\Entity\MailingList;

use CommonBundle\Entity\Acl\Role;
use Doctrine\ORM\Mapping as ORM;
use MailBundle\Entity\MailingList;

/**
 * This entity maps admin roles to mailing lists.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\AdminRoleMap")
 * @ORM\Table(name="mail_lists_admin_roles_map")
 */
class AdminRoleMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var MailingList The list of the mapping
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\MailingList")
     * @ORM\JoinColumn(name="list", referencedColumnName="id")
     */
    private $list;

    /**
     * @var Role The role of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="name")
     */
    private $role;

    /**
     * @var boolean The flag whether the members of the role are allowed to edit the list of admins of the list too.
     *
     * @ORM\Column(name="edit_admin", type="boolean")
     */
    private $editAdmin;

    /**
     * @param MailingList  $list      The list of the mapping
     * @param Role|null    $role      The role of the mapping
     * @param boolean|null $editAdmin The flag whether the members of the role are allowed to edit the list of admins of the list too.
     */
    public function __construct(MailingList $list, Role $role = null, $editAdmin = null)
    {
        $this->list = $list;
        $this->role = $role;
        $this->editAdmin = $editAdmin;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MailingList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param  Role $role
     * @return self
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return boolean
     */
    public function canEditAdmin()
    {
        return $this->editAdmin;
    }

    /**
     * @param  boolean $editAdmin
     * @return self
     */
    public function setEditAdmin($editAdmin)
    {
        $this->editAdmin = $editAdmin;

        return $this;
    }
}
