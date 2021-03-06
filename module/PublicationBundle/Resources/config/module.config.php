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

namespace PublicationBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('site', 'validator'),
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'titleeditionhtml' => Component\Validator\Title\Edition\Html::class,
                'titleEditionHtml' => Component\Validator\Title\Edition\Html::class,
                'TitleEditionHtml' => Component\Validator\Title\Edition\Html::class,
                'titleeditionpdf'  => Component\Validator\Title\Edition\Pdf::class,
                'titleEditionPdf'  => Component\Validator\Title\Edition\Pdf::class,
                'TitleEditionPdf'  => Component\Validator\Title\Edition\Pdf::class,
                'titlepublication' => Component\Validator\Title\Publication::class,
                'titlePublication' => Component\Validator\Title\Publication::class,
                'TitlePublication' => Component\Validator\Title\Publication::class,
            ),
        ),
    )
);
