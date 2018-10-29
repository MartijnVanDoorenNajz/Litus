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

namespace FormBundle\Repository;

use CommonBundle\Entity\User\Person;
use FormBundle\Entity\Node\Form;
use FormBundle\Entity\Node\Group;

/**
 * Viewermap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ViewerMap extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findOneByPersonAndForm(Person $person, Form $form)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->innerJoin('n.form', 'f')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('n.person', ':person'),
                    $query->expr()->eq('n.form', ':form')
                )
            )
            ->setParameter('person', $person)
            ->setParameter('form', $form)
            ->orderBy('f.startDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByFormQuery(Form $form)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->where(
                $query->expr()->eq('n.form', ':form')
            )
            ->setParameter('form', $form)
            ->getQuery();
    }

    public function findAllByPersonQuery(Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->innerJoin('n.form', 'f')
            ->where(
                $query->expr()->eq('n.person', ':person')
            )
            ->setParameter('person', $person)
            ->orderBy('f.startDate', 'DESC')
            ->getQuery();
    }

    public function findAllByGroupAndPersonQuery(Group $group, Person $person)
    {
        $forms = array(0);
        foreach ($group->getForms() as $form) {
            $forms[] = $form->getForm()->getId();
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('n.person', ':person'),
                    $query->expr()->in('n.form', $forms)
                )
            )
            ->setParameter('person', $person)
            ->getQuery();
    }

    public function findAllGroupsByPerson(Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $forms = $query->select('f.id')
            ->from('FormBundle\Entity\ViewerMap', 'n')
            ->innerJoin('n.form', 'f')
            ->where(
                $query->expr()->eq('n.person', ':person')
            )
            ->setParameter('person', $person)
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach ($forms as $form) {
            $ids[] = $form['id'];
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        $mappings = $query->select('m')
            ->from('FormBundle\Entity\Node\Group\Mapping', 'm')
            ->innerJoin('m.form', 'f')
            ->where(
                $query->expr()->in('f.id', $ids)
            )
            ->orderBy('f.startDate', 'DESC')
            ->getQuery()
            ->getResult();

        $groups = array();
        foreach ($mappings as $mapping) {
            $groups[$mapping->getGroup()->getId()] = $mapping->getGroup();
        }

        return $groups;
    }

    public function findOneByPersonAndGroup(Person $person, Group $group)
    {
        if (sizeof($group->getForms()) == 0) {
            return null;
        }

        return $this->findOneByPersonAndForm($person, $group->getForms()[0]->getForm());
    }
}
