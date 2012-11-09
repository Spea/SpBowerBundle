<?php

/*
 * This file is part of the Sp/BowerBundle.
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
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sp:bower:install')
            ->setDescription('Install all bower dependencies.')
            ->addArgument('package', InputArgument::OPTIONAL, 'The package name to install')
            ->addOption('bundle', 'b', InputOption::VALUE_REQUIRED, 'The bundle to install the package to')
            ->setHelp(<<<EOT
The <info>sp:bower:install</info> command installs bower dependencies for every bundle:

  <info>php app/console sp:bower:install</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bowerManager = $this->getContainer()->get('sp_bower.bower.manager');
        $bower = $this->getContainer()->get('sp_bower.bower');
        $kernel = $this->getContainer()->get('kernel');
        $callback = function($type, $data) use($output) {
            $output->write($data);
        };


        if (($package = $input->getArgument('package')) !== null) {
            if (($bundle = $input->getOption('bundle')) === null) {
                throw new \InvalidArgumentException('You must specify a bundle to install the package to');
            }

            $target = $kernel->getBundle($bundle)->getPath() .'/Resources/public/';

            return $this->getBower()->install($package, new DirectoryResource($target), $callback);
        }


        foreach ($this->getBowerManager()->getComponents() as $component) {
            $this->getBower()->install($component['src'], $component['target'], $callback);
        }
    }

    /**
     * @return \Sp\BowerBundle\Bower\BowerManager
     */
    protected function getBowerManager()
    {
        return $this->getContainer()->get('sp_bower.bower.manager');
    }

    /**
     * @return \Sp\BowerBundle\Bower\Bower
     */
    protected function getBower()
    {
        return $this->getContainer()->get('sp_bower.bower');
    }

}
