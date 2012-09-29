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

namespace PublicationBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Bank\BankDevice,
    CommonBundle\Entity\General\Bank\MoneyUnit,
    CommonBundle\Entity\General\Config,
    CudiBundle\Entity\Articles\Options\Binding,
    CudiBundle\Entity\Articles\Options\Color,
    CudiBundle\Entity\Sales\PayDesk,
    DateInterval,
    DateTime,
    Exception;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'publication.bakske_id',
                    'value'       => '1',
                    'description' => 'The id of the publication that represents the weekly magazine.',
                ),
                array(
                    'key'         => 'publication.bakske_mail',
                    'value'       => 'bakske@vtk.be',
                    'description' => 'The bakske mail address.',
                ),
                array(
                    'key'         => 'publication.bakske_mail_name',
                    'value'       => 'Het Bakske',
                    'description' => 'The bakske mail name.',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'publicationbundle' => array(
                    'admin_publication' => array(
                        'add', 'delete', 'edit', 'manage',
                    ),
                    'admin_edition' => array(
                        'add', 'delete', 'manage'
                    ),
                )
            )
        );
    }
}
