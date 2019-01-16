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

namespace TicketBundle\Repository;

use TicketBundle\Entity\Event;
use TicketBundle\Entity\Category;
use TicketBundle\Entity\Option;
use CommonBundle\Entity\User\Person;

/**
 * Ticket
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Ticket extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{

   public function findAllByEventQuery(Event $event)
   {
       $query = $this->getEntityManager()->createQueryBuilder();
       $resultSet = $query->select('t')
           ->from('TicketBundle\Entity\Ticket', 't')
           ->join('t.orderEntity', 'o')
           ->where(
              $query->expr()->eq('o.event', ':event')
           )
           ->setParameter('event', $event->getId())
           ->getQuery();

       return $resultSet;
   }

   public function findAllByEventAndPersonQuery(Event $event, Person $person)
   {
       $query = $this->getEntityManager()->createQueryBuilder();
       $resultSet = $query->select('t')
           ->from('TicketBundle\Entity\Ticket', 't')
           ->join('t.orderEntity', 'o')
           ->where(
              $query->expr()->andX(
                   $query->expr()->eq('o.event', ':event'),
                   $query->expr()->eq('t.person', ':person')
               )
           )
           ->setParameter('event', $event->getId())
           ->setParameter('person', $person->getId())
           ->getQuery();
       return $resultSet;
   }

   public function findAllByEventAndCategoryQuery(Event $event, Category $category)
   {
       $query = $this->getEntityManager()->createQueryBuilder();
       $resultSet = $query->select('t')
           ->from('TicketBundle\Entity\Ticket', 't')
           ->join('t.orderEntity', 'o')
           ->where(
                $query->expr()->andX(
                   $query->expr()->eq('o.event', ':event'),
                   $query->expr()->eq('t.category', ':category')
               )           
              )
           ->setParameter('event', $event->getId())
           ->setParameter('category', $category->getId())
           ->getQuery();
        return $resultSet;

   }

   public function countAllByEventAndCategoryQuery(Event $event, Category $category)
   {
       $query = $this->getEntityManager()->createQueryBuilder();
       $resultSet = $query->select('COUNT(t.id)')
           ->from('TicketBundle\Entity\Ticket', 't')
           ->join('t.orderEntity', 'o')
           ->join('t.option', 'op')
           ->where(
                $query->expr()->andX(
                  $query->expr()->eq('o.event', ':event'),
                  $query->expr()->eq('op.category', ':category')
                )           
              )
           ->setParameter('event', $event->getId())
           ->setParameter('category', $category->getId())
           ->getQuery();
       return $resultSet;
   }

   public function getAllByEventAndStatusQuery(Event $event, $status)
   {
      $query = $this->getEntityManager()->createQueryBuilder();
      $resultSet = $query->select('t')
           ->from('TicketBundle\Entity\Ticket', 't')
           ->join('t.orderEntity', 'o')
           ->where(
                $query->expr()->andX(
                  $query->expr()->eq('o.event', ':event'),
                  $query->expr()->eq('t.status', ':status')
                )           
              )
           ->setParameter('event', $event->getId())
           ->setParameter('status', $status)
           ->getQuery();
       return $resultSet;
   }

   public function getAllByEventAndOptionAndStatusQuery(Event $event, Option $option, $status)
   {
      $query = $this->getEntityManager()->createQueryBuilder();
      $resultSet = $query->select('t')
           ->from('TicketBundle\Entity\Ticket', 't')
           ->join('t.orderEntity', 'o')
           ->where(
              $query->expr()->andX(
                $query->expr()->andX(
                  $query->expr()->eq('o.event', ':event'),
                  $query->expr()->eq('t.option', ':option')
                ),
                $query->expr()->eq('t.status', ':status')         
              )
            )
           ->setParameter('event', $event->getId())
           ->setParameter('option', $option->getId())
           ->setParameter('status', $status)
           ->getQuery();
       return $resultSet;
   }
//
//    public function findAllByEventAndPersonName(EventEntity $event, $name)
//    {
//        $query = $this->getEntityManager()->createQueryBuilder();
//        $resultSet = $query->select('t')
//            ->from('TicketBundle\Entity\Ticket', 't')
//            ->leftJoin('t.guestInfo', 'g')
//            ->leftJoin('t.person', 'p')
//            ->where(
//                $query->expr()->andX(
//                    $query->expr()->eq('t.event', ':event'),
//                    $query->expr()->orX(
//                        $query->expr()->eq('t.status', ':booked'),
//                        $query->expr()->eq('t.status', ':sold')
//                    ),
//                    $query->expr()->orX(
//                        $query->expr()->orX(
//                            $query->expr()->like(
//                                $query->expr()->concat(
//                                    $query->expr()->lower($query->expr()->concat('p.firstName', "' '")),
//                                    $query->expr()->lower('p.lastName')
//                                ),
//                                ':name'
//                            ),
//                            $query->expr()->like(
//                                $query->expr()->concat(
//                                    $query->expr()->lower($query->expr()->concat('p.lastName', "' '")),
//                                    $query->expr()->lower('p.firstName')
//                                ),
//                                ':name'
//                            )
//                        ),
//                        $query->expr()->orX(
//                            $query->expr()->like(
//                                $query->expr()->concat(
//                                    $query->expr()->lower($query->expr()->concat('g.firstName', "' '")),
//                                    $query->expr()->lower('g.lastName')
//                                ),
//                                ':name'
//                            ),
//                            $query->expr()->like(
//                                $query->expr()->concat(
//                                    $query->expr()->lower($query->expr()->concat('g.lastName', "' '")),
//                                    $query->expr()->lower('g.firstName')
//                                ),
//                                ':name'
//                            )
//                        )
//                    )
//                )
//            )
//            ->setParameter('event', $event)
//            ->setParameter('booked', 'booked')
//            ->setParameter('sold', 'sold')
//            ->setParameter('name', '%' . strtolower($name) . '%')
//            ->getQuery()
//            ->getResult();
//
//        $tickets = array();
//        foreach ($resultSet as $ticket) {
//            $tickets[$ticket->getFullName() . '-' . $ticket->getId()] = $ticket;
//        }
//
//        ksort($tickets);
//
//        return $tickets;
//    }
//
//    public function findAllByEventAndOption(EventEntity $event, $option)
//    {
//        $query = $this->getEntityManager()->createQueryBuilder();
//        $resultSet = $query->select('t')
//            ->from('TicketBundle\Entity\Ticket', 't')
//            ->leftJoin('t.option', 'o')
//            ->where(
//                $query->expr()->andX(
//                    $query->expr()->eq('t.event', ':event'),
//                    $query->expr()->orX(
//                        $query->expr()->eq('t.status', ':booked'),
//                        $query->expr()->eq('t.status', ':sold')
//                    ),
//                    $query->expr()->like($query->expr()->lower('o.name'), ':option')
//                )
//            )
//            ->setParameter('event', $event)
//            ->setParameter('booked', 'booked')
//            ->setParameter('sold', 'sold')
//            ->setParameter('option', '%' . strtolower($option) . '%')
//            ->getQuery()
//            ->getResult();
//
//        $tickets = array();
//        foreach ($resultSet as $ticket) {
//            $tickets[$ticket->getFullName() . '-' . $ticket->getId()] = $ticket;
//        }
//
//        ksort($tickets);
//
//        return $tickets;
//    }
//
//    public function findAllByEventAndOrganization(EventEntity $event, $organization, AcademicYear $academicYear)
//    {
//        $query = $this->getEntityManager()->createQueryBuilder();
//        $resultSet = $query->select('p.id')
//            ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
//            ->innerJoin('m.academic', 'p')
//            ->innerJoin('m.organization', 'o')
//            ->where(
//                $query->expr()->andX(
//                    $query->expr()->eq('m.academicYear', ':academicYear'),
//                    $query->expr()->like($query->expr()->lower('o.name'), ':organization')
//                )
//            )
//            ->setParameter('academicYear', $academicYear)
//            ->setParameter('organization', '%' . strtolower($organization) . '%')
//            ->getQuery()
//            ->getResult();
//
//        $ids = array(0);
//        foreach ($resultSet as $item) {
//            $ids[] = $item['id'];
//        }
//
//        $query = $this->getEntityManager()->createQueryBuilder();
//        $resultSet = $query->select('t')
//            ->from('TicketBundle\Entity\Ticket', 't')
//            ->innerJoin('t.person', 'p')
//            ->where(
//                $query->expr()->andX(
//                    $query->expr()->eq('t.event', ':event'),
//                    $query->expr()->orX(
//                        $query->expr()->eq('t.status', ':booked'),
//                        $query->expr()->eq('t.status', ':sold')
//                    ),
//                    $query->expr()->in('p.id', $ids)
//                )
//            )
//            ->setParameter('event', $event)
//            ->setParameter('booked', 'booked')
//            ->setParameter('sold', 'sold')
//            ->getQuery()
//            ->getResult();
//
//        $tickets = array();
//        foreach ($resultSet as $ticket) {
//            $tickets[$ticket->getFullName() . '-' . $ticket->getId()] = $ticket;
//        }
//
//        ksort($tickets);
//
//        return $tickets;
//    }
}
