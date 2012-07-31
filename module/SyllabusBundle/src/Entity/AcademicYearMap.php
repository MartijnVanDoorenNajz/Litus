<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;

/**
 * @Entity(repositoryClass="SyllabusBundle\Repository\AcademicYearMap")
 * @Table(name="syllabus.studies_academic_year_map")
 */
class AcademicYearMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var \SyllabusBundle\Entity\Study The study of the mapping
     *
     * @ManyToOne(targetEntity="SyllabusBundle\Entity\Study")
     * @JoinColumn(name="study", referencedColumnName="id")
     */
    private $study;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The year of the mapping
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;
    
    /**
     * @param \SyllabusBundle\Entity\Study $study
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     */
    public function __construct(Study $study, AcademicYear $academicYear)
    {
        $this->study = $study;
        $this->academicYear = $academicYear;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \SyllabusBundle\Entity\Study
     */
    public function getStudy()
    {
        return $this->study;
    }
    
    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}
