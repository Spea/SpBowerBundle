<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class SpBowerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($config['register_assets']) {
            $bundles = $container->getParameter('kernel.bundles');
            if (!isset($bundles['AsseticBundle'])) {
                throw new \RuntimeException('The SpBowerBundle requires the AsseticBundle, please make sure to enable it in your AppKernel.');
            }

            $loader->load('assetic.xml');
        }

        $container->setParameter('sp_bower.bower.bin', $config['bin']);
        $this->loadPathsInformation($config['paths'], $container);
    }

    protected function loadPathsInformation($paths, ContainerBuilder $container)
    {
        $bowerManager = $container->getDefinition('sp_bower.bower_manager');
        $filesystem = new Filesystem();

        foreach ($paths as $bundleName => $pathConfig) {
            $bundle = null;
            foreach ($container->getParameter('kernel.bundles') as $name => $class) {
                if ($bundleName === $name) {
                    $bundle = new \ReflectionClass($class);

                    break;
                }
            }

            if (null === $bundle) {
                throw new \InvalidArgumentException(sprintf('Bundle "%s" does not exist or it is not enabled.', $bundleName));
            }

            $bundleDir = dirname($bundle->getFilename());
            if (!$filesystem->isAbsolutePath($pathConfig['config_dir'])) {
                $pathConfig['config_dir'] = $bundleDir.DIRECTORY_SEPARATOR.$pathConfig['config_dir'];
            }

            if ($filesystem->isAbsolutePath($pathConfig['asset_dir'])) {
                $pathConfig['asset_dir'] = $filesystem->makePathRelative($pathConfig['asset_dir'], $pathConfig['config_dir']);
            }

            $configuration = new Definition('%sp_bower.bower.configuration.class%');
            $configuration->addMethodCall('setDirectory', array($pathConfig['asset_dir']));
            $configuration->addMethodCall('setJsonFile', array($pathConfig['json_file']));
            $configuration->addMethodCall('setEndpoint', array($pathConfig['endpoint']));
            $bowerManager->addMethodCall('addPath', array($pathConfig['config_dir'], $configuration));
        }
    }

    private function createDirectoryResourceDefinition($src)
    {
        $definition = new Definition('%sp_bower.directory_resource.class%', array($src));

        return $definition;
    }

}
