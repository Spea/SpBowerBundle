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
            ->setHelp(<<<EOT
The <info>sp:bower:install</info> command installs bower dependencies for every bundle:

  <info>php app/console sp:bower:install</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bower = $this->getContainer()->get('sp_bower.bower');
    }

}
