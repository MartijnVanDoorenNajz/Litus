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

namespace FormBundle\Entity\Node\Form;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Field\File as FileField;
use FormBundle\Entity\Node\Entry;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Form\Form")
 * @ORM\Table(name="nodes_forms_forms")
 */
class Form extends \FormBundle\Entity\Node\Form
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'form';
    }

    /**
     * @param  Entry    $entry
     * @param  Language $language
     * @return string
     */
    protected function getSummary(Entry $entry, Language $language)
    {
        $fieldEntries = $this->entityManager
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByFormEntry($entry);

        $result = '';
        foreach ($fieldEntries as $fieldEntry) {
            $result .= $fieldEntry->getField()->getLabel($language) . ': ';
            $result .= $fieldEntry->getField() instanceof FileField ? $fieldEntry->getReadableValue() : $fieldEntry->getValueString($language);
            $result .= PHP_EOL;
        }

        return $result;
    }
}
