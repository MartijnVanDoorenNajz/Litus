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

return array(
    'submenus' => array(
        'Shifts' => array(
            'subtitle' => array('Counter', 'Rankings', 'Shifts', 'Registration Shifts'),
            'items'    => array(
                'shift_admin_shift_counter' => array(
                    'action' => 'index',
                    'title'  => 'Counter',
                ),
                'shift_admin_shift_ranking' => array(
                    'action' => 'index',
                    'title'  => 'Ranking',
                ),
                'shift_admin_shift_weekly_change' => array(
                    'action' => 'index',
                    'title'  => 'Weekly Change',
                ),
                'shift_admin_shift' => array(
                    'action' => 'manage',
                    'title'  => 'Shifts',
                ),
                'shift_admin_registration_shift' => array(
                    'action' => 'manage',
                    'title'  => 'Registration Shifts',
                ),
            ),
            'controllers' => array('shift_admin_unit'),
        ),
    ),
);
