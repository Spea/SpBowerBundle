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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @author Luis Hdez <luis.munoz.hdez@gmail.com>
 */
class ListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sp:bower:list')
            ->setDescription('List available packages')
            ->setHelp(<<<EOT
The <info>sp:bower:list</info> command lists all packages and their resources.

  <info>php app/console sp:bower:list</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bowerResource = $this->getBowerResource();

        $content = $bowerResource->getContent();

        foreach ($content as $resource => $data) {
            $output->writeln('<comment>@'.$resource.'</comment>');

            foreach ($data[0] as $file) {
                $output->writeln('  '.$file);
            }
        }
    }

    /**
     * @return \Sp\BowerBundle\Assetic\BowerResource
     */
    protected function getBowerResource()
    {
        return $this->getContainer()->get('sp_bower.assetic.bower_resource');
    }
}
