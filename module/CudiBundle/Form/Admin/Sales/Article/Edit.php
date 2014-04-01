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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Sales\Article;

use CudiBundle\Component\Validator\Sales\Article\Barcodes\Unique as UniqueBarcodeValidator,
    CudiBundle\Entity\Sale\Article,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Sale Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Sales\Article\Add
{
    /**
     * @var \CudiBundle\Entity\Sale\Article
     */
    private $_article;

    /**
     * @param \Doctrine\ORM\EntityManager     $entityManager The EntityManager instance
     * @param \CudiBundle\Entity\Sale\Article $article
     * @param null|string|int                 $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Article $article, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_article = $article;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'article_edit');
        $this->add($field);

        $this->populateFromArticle($article);

        $membershipArticles = unserialize(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        if (in_array($article->getId(), $membershipArticles)) {
            $this->get('bookable')->setAttribute('disabled', 'disabled');
            $this->get('unbookable')->setAttribute('disabled', 'disabled');
        }
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('barcode');
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'barcode',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'barcode',
                            'options' => array(
                                'adapter'     => 'Ean12',
                                'useChecksum' => false,
                            ),
                        ),
                        new UniqueBarcodeValidator($this->_entityManager, $this->_article),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
