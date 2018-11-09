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

use Ko\Process;
use Ko\ProcessManager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Start all WebSockets.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Sockets extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this->setName('common:sockets')
            ->setDescription('Start all WebSockets')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command starts all WebSockets.
EOT
            );
    }

    protected function executeCommand()
    {
        $manager = new ProcessManager();
        $logFile = fopen($this->getLogFile(), 'a', false);

        $commands = $this->getApplication()->all('socket');
        foreach ($commands as $command) {
            $isEnabled = $command->run(
                new StringInput('--is-enabled'),
                new NullOutput()
            );

            if ($isEnabled === 1) {
                continue;
            }

            // Close parent connection to force reconnection in child process
            $connection = $this->getEntityManager()->getConnection();
            $connection->close();

            $this->write('Starting <comment>' . $command->getName() . '</comment>...');

            $manager->fork(
                function (Process $p) use ($command, $logFile) {
                    $command->run(
                        new StringInput(''),
                        new StreamOutput($logFile)
                    );
                }
            );

            $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
        }

        $manager->wait();
    }

    private function getLogFile()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.sockets_log');
    }
}
