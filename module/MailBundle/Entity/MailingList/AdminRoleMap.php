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

namespace MailBundle\Entity\MailingList;

use CommonBundle\Entity\Acl\Role,
    DateTime,
    Doctrine\ORM\Mapping as ORM,
    MailBundle\Entity\MailingList;

/**
 * This entity maps admins to mailinglists.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\AdminRoleMap")
 * @ORM\Table(name="mail.lists_admin_roles")
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
     * @var \MailBundle\Entity\MailingList The list of the mapping
     *
     * @ORM\ManyToOne(targetEntity="MailBundle\Entity\MailingList")
     * @ORM\JoinColumn(name="list", referencedColumnName="id")
     */
    private $list;

    /**
     * @var \CommonBundle\Entity\Acl\Role The role of the mapping
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
     * @param \MailBundle\Entity\MailingList The list of the mapping
     * @param \CommonBundle\Entity\Acl\Role $role The role of the mapping
     * @param boolean $editAdmin The flag whether the members of the role are allowed to edit the list of admins of the list too.
     */
    public function __construct(MailingList $list, Role $role, $editAdmin)
    {
        $this->list = $list;
        $this->role = $role;
        $this->editAdmin = $editAdmin;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @return \MailBundle\Entity\MailingList
     */
    public function getList() {
        return $this->list;
    }

    /**
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * @return boolean
     */
    public function canEditAdmin() {
        return $this->editAdmin;
    }
}