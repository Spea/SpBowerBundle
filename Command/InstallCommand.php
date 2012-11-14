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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

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
        $kernel = $this->getContainer()->get('kernel');
        $callback = function($type, $data) use($output) {
            $output->write($data);
        };

        foreach ($bowerManager->getPaths() as $configDir => $configuration) {
            $bower->init($configDir, $configuration);
            $bower->install($configDir, $callback);
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
