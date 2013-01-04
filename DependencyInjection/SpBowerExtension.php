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
use Symfony\Component\DependencyInjection\Definition;
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
     * @param array $configs
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
        $container->setParameter('sp_bower.install_on_warmup', $config['install_on_warmup']);
        $this->loadBundlesInformation($config['bundles'], $container);
    }

    protected function loadBundlesInformation($bundles, ContainerBuilder $container)
    {
        $bowerManager = $container->getDefinition('sp_bower.bower_manager');
        $filesystem = new Filesystem();

        foreach ($bundles as $bundleName => $bundleConfig) {
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
            if (!$filesystem->isAbsolutePath($bundleConfig['config_dir'])) {
                $bundleConfig['config_dir'] = $bundleDir.DIRECTORY_SEPARATOR.$bundleConfig['config_dir'];
            }

            if ($filesystem->isAbsolutePath($bundleConfig['asset_dir'])) {
                $bundleConfig['asset_dir'] = $filesystem->makePathRelative($bundleConfig['asset_dir'], $bundleConfig['config_dir']);
            }

            $configuration = new Definition('%sp_bower.bower.configuration.class%');
            $configuration->addArgument($bundleConfig['config_dir']);
            $configuration->addMethodCall('setAssetDirectory', array($bundleConfig['asset_dir']));
            $configuration->addMethodCall('setJsonFile', array($bundleConfig['json_file']));
            $configuration->addMethodCall('setEndpoint', array($bundleConfig['endpoint']));
            $bowerManager->addMethodCall('addBundle', array($bundleName, $configuration));
        }
    }

    private function createDirectoryResourceDefinition($src)
    {
        $definition = new Definition('%sp_bower.directory_resource.class%', array($src));

        return $definition;
    }

}
