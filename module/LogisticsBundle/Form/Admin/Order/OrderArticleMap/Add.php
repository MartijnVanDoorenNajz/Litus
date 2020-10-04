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

namespace LogisticsBundle\Form\Admin\Order\OrderArticleMap;

/**
 * Add an OrderArticleMap
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var array All possible articles
     */
    private $articles;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'articles',
                'label'      => 'Articles',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                    'style'    => 'max-width: 100%;height: 600px;',
                    'options'  => $this->getArticleNames(),
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @return array
     */
    private function getArticleNames()
    {
        $articleNames = array();
        foreach ($this->articles as $article) {
            $articleNames[$article->getId()] = $article->getCategory() . ' - ' . $article->getTitle();
        }

        return $articleNames;
    }

    /**
     * @param  array All possible studies
     * @return self
     */
    public function setArticles(array $articles)
    {
        $this->articles = $articles;

        return $this;
    }
}
