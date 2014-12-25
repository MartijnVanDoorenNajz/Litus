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

namespace BrBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('corporate', 'career', 'cv'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'invokables' => array(
                'contract_bullet'   => 'BrBundle\Component\Validator\Contract\Bullet',
                'company_logo_type' => 'BrBundle\Component\Validator\Logo\Type',
                'company_name'      => 'BrBundle\Component\Validator\CompanyName',
                'product_name'      => 'BrBundle\Component\Validator\ProductName',
            ),
        ),
    )
);
