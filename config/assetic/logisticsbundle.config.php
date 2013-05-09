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

return array(
    'controllers'  => array(
        'logistics_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'logistics_admin_driver' => array(
            '@common_jquery',
            '@common_typeahead_remote',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@minicolor_css',
            '@minicolor_js',
        ),
        'logistics_admin_van_reservation' => array(
            '@common_jquery',
            '@common_typeahead_remote',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'logistics_index' => array(
            '@common_jquery',
            '@common_jqueryui',
            '@common_typeahead_remote',
            '@fullcalendar_css',
            '@logistics_js',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@logistics_css',
        ),
    ),
    'routes' => array(),
);
