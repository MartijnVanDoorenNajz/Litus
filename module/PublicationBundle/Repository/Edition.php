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

namespace PublicationBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;
use PublicationBundle\Entity\Publication as PublicationEntity;

/**
 * Edition
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Edition extends EntityRepository
{
    public function findAllYearsByPublicationQuery(PublicationEntity $publication)
    {
        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('e')
            ->from('PublicationBundle\Entity\Edition', 'e')
            ->where(
                $subQuery->expr()->andX(
                    $subQuery->expr()->eq('e.academicYear', 'y'),
                    $subQuery->expr()->eq('e.publication', ':publication')
                )
            );

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('y')
            ->from('CommonBundle\Entity\General\AcademicYear', 'y')
            ->where(
                $query->expr()->exists(
                   $subQuery->getDQL()
               )
            )
            ->orderBy('y.universityStart', 'DESC')
            ->setParameter('publication', $publication)
            ->getQuery();

        return $resultSet;
    }
}
