<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace PublicationBundle\Form\Admin\Edition\Pdf;

use CommonBundle\Component\Form\Admin\Element\File,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    PublicationBundle\Component\Validator\Title\Edition\Pdf as TitleValidator,
    PublicationBundle\Entity\Publication,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to add a new Publication
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var \PublicationBundle\Entity\Publication The publication
     */
    private $_publication = null;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The current academic year
     */
    private $_academicYear = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \PublicationBundle\Entity\Publication $publication The publication to add an edition to.
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The current academic year.
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Publication $publication, AcademicYear $academicYear, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_publication = $publication;
        $this->_academicYear = $academicYear;

        $this->setAttribute('id', 'uploadFile');
        $this->setAttribute('enctype', 'multipart/form-data');

        $field = new Text('title');
        $field->setLabel('Title')
            ->setRequired(true);
        $this->add($field);

        $field = new Text('date');
        $field->setLabel('Date')
            ->setAttribute('placeholder', 'dd/mm/yyyy')
            ->setRequired();
        $this->add($field);

        $field = new File('file');
        $field->setLabel('File')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'pdf_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'title',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new TitleValidator($this->_entityManager, $this->_publication, $this->_academicYear)
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'date',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
