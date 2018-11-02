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

use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver as ODMAnnotationDriver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver as ORMAnnotationDriver;

if (!file_exists(__DIR__ . '/../database.config.php')) {
    throw new \RuntimeException(
        'The database configuration file (' . (__DIR__ . '/../database.config.php') . ') was not found'
    );
}

$databaseConfig = include __DIR__ . '/../database.config.php';

return array(
    'doctrine' => array(
        'cache' => array(
            'memcached' => array(
                'namespace' => getenv('ORGANIZATION') . '_Litus_Doctrine',
                'servers'   => array(
                    array('localhost', 11211),
                ),
            ),
        ),
        'configuration' => array(
            'odm_default' => array(
                'generate_proxies'   => true,
                'proxy_dir'          => 'data/proxies',
                'generate_hydrators' => true,
                'hydrator_dir'       => 'data/hydrators',
                'default_db'         => $databaseConfig['document']['dbname'],
            ),
            'orm_default' => array(
                'metadataCache'    => getenv('APPLICATION_ENV') != 'development' ? 'memcached' : 'array',
                'queryCache'       => getenv('APPLICATION_ENV') != 'development' ? 'memcached' : 'array',
                'resultCache'      => getenv('APPLICATION_ENV') != 'development' ? 'memcached' : 'array',
                'hydration_cache'  => getenv('APPLICATION_ENV') != 'development' ? 'memcached' : 'array',
                'generate_proxies' => getenv('APPLICATION_ENV') == 'development',
                'proxyDir'         => 'data/proxies/',
            ),
        ),
        'connection' => array(
            'odm_default' => array(
                'server'   => $databaseConfig['document']['server'],
                'port'     => $databaseConfig['document']['port'],
                'user'     => $databaseConfig['document']['user'],
                'password' => $databaseConfig['document']['password'],
                'dbname'   => $databaseConfig['document']['dbname'],
                'options'  => $databaseConfig['document']['options'],
            ),
            'orm_default' => array(
                'driverClass' => $databaseConfig['relational']['driver'],
                'params'      => array(
                    'host'     => $databaseConfig['relational']['host'],
                    'port'     => $databaseConfig['relational']['port'],
                    'user'     => $databaseConfig['relational']['user'],
                    'password' => $databaseConfig['relational']['password'],
                    'dbname'   => $databaseConfig['relational']['dbname'],
                ),
            ),
        ),
        'driver' => array(
            'odm_annotation_driver' => array(
                'class' => ODMAnnotationDriver::class,
            ),
            'orm_annotation_driver' => array(
                'class' => ORMAnnotationDriver::class,
            ),
        ),
    ),
);
