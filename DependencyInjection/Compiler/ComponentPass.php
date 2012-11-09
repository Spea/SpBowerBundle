<?php

/*
 * This file is part of the Sp/BowerBundle.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class ComponentPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $manager = $container->getDefinition('sp_bower.bower.manager');
        foreach ($bundles as $bundle) {
            $rc = new \ReflectionClass($bundle);
            $bundleDir = dirname($rc->getFileName());
            if (is_file($src = $bundleDir .'/Resources/config/bower/component.json') && is_dir($target = $bundleDir .'/Resources/public' )) {
                $src = dirname($src);
                $manager->addMethodCall('addComponent', array($this->createDirectoryResourceDefinition($src), $this->createDirectoryResourceDefinition($target)));
            }
        }
    }

    private function createDirectoryResourceDefinition($src)
    {
        $definition = new Definition('%sp_bower.directory_resource.class%', array($src));

        return $definition;
    }

    private function createFileResourceDefinition($src)
    {
        $definition = new Definition('%sp_bower.file_resource.class%', array($src));

        return $definition;
    }
}
