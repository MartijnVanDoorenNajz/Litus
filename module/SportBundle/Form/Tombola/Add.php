<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Hannes Vandecasteele <hannes.vandecasteele@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SportBundle\Form\Tombola;

/**
 * Add an individual runner and select happy hours for the tombola
 *
 * @author Hannes Vandecasteele <hannes.vandecasteele@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'SportBundle\Hydrator\Runner';

    protected $happyHours;
    
    protected function getHappyHours()
    {
        return $this->happyHours;
    }

    public function setHappyHours($happyHours)
    {
        $this->happyHours = $happyHours;
    }

	public function init()
	{
		parent::init();

		$this->add(array(
		    'type'       => 'fieldset',
		    'name'       => 'information',
		    'label'      => 'Information',
		    'attributes' => array(
		        'id' => 'information',
		    ),
		    'elements'   => array(
		        array(
		            'type'       => 'text',
		            'name'       => 'university_identification',
		            'label'      => 'University Identification',
		            'attributes' => array(
		                'id'           => 'university_identification',
		                'autocomplete' => 'off',
		            ),
		            'options'    => array(
		                'input' => array(
		                    'filters'  => array(
		                        array('name' => 'StringTrim'),
		                    ),
		                ),
		            ),
		        ),
                array(
                    'type' => 'select',
                    'name' => 'happy_hour',
                    'label' => 'Happy Hour',
                    'attributes' => array(
                        'options' => $this->getHappyHours(),
                    ),
                ),
		    ),
		));

		$this->addSubmit('Register for Tombola');
    }

    private function getDepartments()
    {
        $departments = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Department')
            ->findAll();

        $array = array('0' => '');
        foreach ($departments as $department) {
            $array[$department->getId()] = $department->getName();
        }

        return $array;
    }
}