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

namespace CommonBundle\Component\Form\Admin\Form;

use CommonBundle\Component\Form\Admin\Fieldset\TabContent;
use CommonBundle\Component\Form\Admin\Fieldset\TabPane;
use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;
use Locale;
use RuntimeException;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Tabbable extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var string The prefix of the tab elements
     */
    private $prefix = '';

    public function init()
    {
        $languages = $this->getLanguages();
        $prefix = $this->getPrefix();

        if (count($languages) === 0) {
            throw new RuntimeException('No languages found!');
        }

        if (count($languages) === 1) {
            $this->initBeforeTabs();

            $this->addTab($this, $languages[0], true);
        } else {
            $defaultLanguage = Locale::getDefault();

            $this->add(
                array(
                    'type'       => 'tabs',
                    'name'       => $prefix . 'languages',
                    'attributes' => array(
                        'id' => $prefix . 'languages',
                    ),
                )
            );

            $tabs = $this->get($prefix . 'languages');
            $tabContent = $this->createTabContent();

            $this->initBeforeTabs();

            foreach ($languages as $language) {
                $abbrev = $language->getAbbrev();

                $pane = $this->createTabPane($tabContent, $prefix . 'tab_' . $abbrev);

                $this->addTab($pane, $language, $abbrev == $defaultLanguage);

                $tabs->addTab(array($language->getName() => $this->escapeTabContentId('#' . $tabContent->getName() . '[' . $prefix . 'tab_' . $abbrev . ']')));
            }
        }

        $this->initAfterTabs();
    }

    /**
     * @return TabContent
     */
    private function createTabContent()
    {
        $this->add(
            array(
                'type' => 'tabcontent',
                'name' => $this->getPrefix() . 'tab_content',
            )
        );

        return $this->get($this->getPrefix() . 'tab_content');
    }

    /**
     * @param  TabContent $tabContent
     * @param  string     $name
     * @return TabPane
     */
    private function createTabPane(TabContent $tabContent, $name)
    {
        $tabContent->add(
            array(
                'type' => 'tabpane',
                'name' => $name,
            )
        );

        return $tabContent->get($name);
    }

    /**
     * @param  string $id The id of the tab content
     * @return string
     */
    private function escapeTabContentId($id)
    {
        return str_replace(array('[', ']'), array('\\[', '\\]'), $id);
    }

    /**
     * @param  string $prefix
     * @return self
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        if ($this->prefix === null || $this->prefix == '') {
            return '';
        }

        return $this->prefix . '_';
    }

    /**
     * Take all actions that init() should perform before adding the tabbed fields.
     */
    protected function initBeforeTabs()
    {
        // NOP
    }

    /**
     * Take all actions that init() should perform after adding the tabbed fields.
     */
    protected function initAfterTabs()
    {
        // NOP
    }

    /**
     * @param  FieldsetInterface $container The tab
     * @param  Language          $language  The language of the tab
     * @param  boolean           $isDefault Whether the language is the default langauge
     * @return null
     */
    abstract protected function addTab(FieldsetInterface $container, Language $language, $isDefault);

    /**
     * @return array
     */
    protected function getLanguages()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }
}
