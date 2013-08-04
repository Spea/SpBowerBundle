<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Command;

use Sp\BowerBundle\Bower\Exception\CommandException;
use Sp\BowerBundle\Bower\Exception\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\Process;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sp:bower:install')
            ->setDescription('Install all bower dependencies.')
            ->setHelp(<<<EOT
The <info>sp:bower:install</info> command installs bower dependencies for every bundle:

  <info>php app/console sp:bower:install</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bowerManager = $this->getBowerManager();
        $bower = $this->getBower();
        $callback = function($type, $data) use ($output) {
            if (Process::ERR != $type) {
                $output->write($data);
            }
        };

        foreach ($bowerManager->getBundles() as $bundle => $configuration) {
            $output->writeln(sprintf('Installing bower dependencies for <comment>"%s"</comment> into <comment>"%s"</comment>', $bundle, $configuration->getAssetDirectory()));
            try {
                $bower->install($configuration, $callback);
            } catch (CommandException $ex) {
                $output->writeln($ex->getCommandError());
                throw new RuntimeException("An error occured while installing dependencies");
            }
        }
    }

    /**
     * @return \Sp\BowerBundle\Bower\BowerManager
     */
    protected function getBowerManager()
    {
        return $this->getContainer()->get('sp_bower.bower_manager');
    }

    /**
     * @return \Sp\BowerBundle\Bower\Bower
     */
    protected function getBower()
    {
        return $this->getContainer()->get('sp_bower.bower');
    }

}
