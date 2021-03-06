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

namespace CudiBundle\Entity\Sale\Session\Restriction;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Session;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Study as StudyEntity;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\Restriction\Study")
 * @ORM\Table(name="cudi_sale_sessions_restrictions_study")
 */
class Study extends \CudiBundle\Entity\Sale\Session\Restriction
{
    /**
     * @var ArrayCollection The value of the restriction
     *
     * @ORM\ManyToMany(targetEntity="SyllabusBundle\Entity\Study")
     * @ORM\JoinTable(name="cudi_sale_sessions_restrictions_studies_map",
     *      joinColumns={@ORM\JoinColumn(name="restriction", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="study", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $studies;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        parent::__construct($session);

        $this->studies = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'study';
    }

    /**
     * @return ArrayCollection
     */
    public function getStudies()
    {
        return $this->studies;
    }

    /**
     * @param  StudyEntity $study
     * @return self
     */
    public function addStudy(StudyEntity $study)
    {
        $this->studies->add($study);

        return $this;
    }

    /**
     * @return string
     */
    public function getReadableValue()
    {
        $value = '';
        foreach ($this->studies as $study) {
            $value .= 'Phase ' . $study->getPhase() . ' - ' . $study->getTitle() . ' ; ';
        }

        return $value;
    }

    /**
     * @param EntityManager $entityManager
     * @param Person        $person
     *
     * @return boolean
     */
    public function canSignIn(EntityManager $entityManager, Person $person)
    {
        $academicYear = AcademicYear::getUniversityYear($entityManager);

        $studies = $entityManager
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($person, $academicYear);

        $allowedStudies = $this->studies->toArray();

        foreach ($studies as $study) {
            foreach ($allowedStudies as $allowedStudy) {
                if ($allowedStudy == $study->getStudy()) {
                    return true;
                }
            }
        }

        return false;
    }
}
