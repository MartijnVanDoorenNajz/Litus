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

namespace CommonBundle\Component\Form;

/**
 * LabelAwareTrait
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
trait LabelAwareTrait
{
    /**
     * @param string $class
     * @return self
     */
    public function addLabelClass($class)
    {
        if ($this->hasLabelClass($class)) {
            return $this;
        }

        $labelClasses = array();
        if (array_key_exists('class', $this->getLabelAttributes())) {
            $labelClasses = explode(' ', $this->getLabelAttributes()['class']);
        }

        $this->setLabelAttributes(
            array_merge(
                $this->getLabelAttributes(),
                array(
                    'class' => implode(' ', array_push($labelClasses, $class))
                )
            )
        );


        return $this;
    }

    /**
     * @param string $class
     * @return boolean
     */
    public function hasLabelClass($class)
    {
        if (!array_key_exists('class', $this->getLabelAttributes())) {
            return false;
        }

        return in_array($class, explode(' ', $this->getLabelAttributes()['class']));
    }
}
