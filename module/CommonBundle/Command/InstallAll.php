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

namespace CommonBundle\Command;

/**
 * Run all installers.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class InstallAll extends \CommonBundle\Component\Console\Command
{
    /**
     * @var string|null The name of the module currently being installed.
     */
    private $currentModule = null;

    protected function configure()
    {
        parent::configure();

        $this->setName('install:all')
            ->setDescription('Install all modules');
    }

    protected function invoke()
    {
        foreach ($this->getModules() as $module) {
            $this->installModule($module);
        }
    }

    protected function getLogName()
    {
        if ($this->currentModule !== null) {
            return $this->currentModule;
        }

        return parent::getLogName();
    }

    protected function getLogNameLength()
    {
        return max(
            array_map(
                function ($value) {
                    return strlen($value);
                },
                $this->getModules()
            )
        );
    }

    protected function getLogNameTag()
    {
        if ($this->currentModule !== null) {
            return 'fg=blue';
        }

        return parent::getLogNameTag();
    }

    private function getModules()
    {
        $config = $this->getConfig()['litus']['install'];

        // CommonBundle has to be first
        return array_merge(
            array('CommonBundle'),
            array_filter(
                array_keys($config),
                function ($v) {
                    return $v != 'CommonBundle';
                }
            )
        );
    }

    private function installModule($module)
    {
        $this->writeln('Installing <comment>' . $module . '</comment>');

        $this->currentModule = $module;

        $this->getServiceLocator()
            ->get($module . '\Component\Module\Installer')
            ->setCommand($this)
            ->install();

        $this->currentModule = null;
    }
}
