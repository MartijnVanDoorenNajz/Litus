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

return array(
    'publicationbundle' => array(
        'publication_admin_publication' => array(
            'add', 'delete', 'edit', 'manage',
        ),
        'publication_admin_edition_html' => array(
            'add', 'delete', 'manage', 'upload'
        ),
        'publication_admin_edition_pdf' => array(
            'add', 'delete', 'manage', 'upload', 'view'
        ),
        'publication_edition_html' => array(
            'view',
        ),
        'publication_archive' => array(
            'overview', 'view', 'year',
        ),
    )
);
