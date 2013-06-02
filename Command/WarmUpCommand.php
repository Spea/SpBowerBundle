<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Luis Hdez <luis.munoz.hdez@gmail.com>
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
class WarmUpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sp:bower:warmup')
            ->setDescription('Warm up all bower mapping dependencies.')
            ->setHelp(<<<EOT
The <info>sp:bower:warmup</info> command warmup bower mapping dependencies for every bundle into the cache:

  <info>php app/console sp:bower:warmup</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bowerManager = $this->getBowerManager();
        $bower = $this->getBower();

        function ($type, $data) use ($output) {
            $output->write($data);
        };

        foreach ($bowerManager->getBundles() as $bundle => $configuration) {
            $output->writeln(sprintf('Mapping bower dependencies for <comment>"%s"</comment> into <comment>"%s"</comment>', $bundle, $configuration->getAssetDirectory()));
            $bower->createDependencyMappingCache($configuration);
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
