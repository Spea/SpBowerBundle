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

use RuntimeException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
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
        if ($config['assetic']['enabled']) {
            $this->registerAsseticConfiguration($config, $container, $loader);
        }

        $cacheDir = $container->getParameterBag()->resolveValue($config['cache_dir']);
        if (!is_dir($cacheDir)) {
            if (false === @mkdir($cacheDir, 0777, true)) {
                throw new RuntimeException(sprintf('Could not create cache directory "%s".', $cacheDir));
            }
        }

        $container->setParameter('sp_bower.cache_dir', $cacheDir);
        if (!$config['keep_bowerrc']) {
            $execListener = $container->getDefinition('sp_bower.exec_listener');
            $execListener->addTag('kernel.event_subscriber');
        }

        $container->setParameter('sp_bower.bower.bin', $config['bin']);
        $container->setParameter('sp_bower.install_on_warmup', $config['install_on_warmup']);
        $this->loadBundlesInformation($config['bundles'], $container);
    }

    /**
     * @param array                                                       $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder     $container
     * @param \Symfony\Component\DependencyInjection\Loader\XmlFileLoader $loader
     *
     * @throws \RuntimeException
     */
    protected function registerAsseticConfiguration(array $config, ContainerBuilder $container, Loader\XmlFileLoader $loader)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!isset($bundles['AsseticBundle'])) {
            throw new \RuntimeException('The SpBowerBundle requires the AsseticBundle, please make sure to enable it in your AppKernel.');
        }

        $loader->load('assetic.xml');

        $resourceDefinition = $container->getDefinition('sp_bower.assetic.bower_resource');
        $resourceDefinition->addMethodCall('setJsFilters', array($config['assetic']['filters']['js']));
        $resourceDefinition->addMethodCall('setCssFilters', array($config['assetic']['filters']['css']));
        $resourceDefinition->addMethodCall('setNestDependencies', array($config['assetic']['nest_dependencies']['all']));
        unset($config['assetic']['nest_dependencies']['all']);

        $this->processPackageFilters($container, $config['assetic']['filters']['packages']);
        $this->processPackageNestDependencies($container    , $config['assetic']['nest_dependencies']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $nestDependencies
     */
    protected function processPackageNestDependencies(ContainerBuilder $container, array $nestDependencies)
    {
        foreach ($nestDependencies as $packageName => $enabled) {
            $definition = $this->createPackageResource($container, $packageName);
            $definition->addMethodCall('setNestDependencies', array($enabled));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $packageFilters
     */
    protected function processPackageFilters(ContainerBuilder $container, array $packageFilters)
    {
        foreach ($packageFilters as $packageName => $filters) {
            $definition = $this->createPackageResource($container, $packageName);
            $definition->addMethodCall('setCssFilters', array($filters['css']));
            $definition->addMethodCall('setJsFilters', array($filters['js']));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $packageName
     *
     * @return Definition
     */
    protected function createPackageResource(ContainerBuilder $container, $packageName)
    {
        $packageResourceId = sprintf("sp_bower.assetic.%s_package_resource", $packageName);
        if ($container->hasDefinition($packageResourceId)) {
            return $container->getDefinition($packageResourceId);
        }

        $definition = new Definition('%sp_bower.assetic.package_resource.class%', array($packageName));
        $container->setDefinition($packageResourceId, $definition);

        $resourceDefinition = $container->getDefinition('sp_bower.assetic.bower_resource');
        $resourceDefinition->addMethodCall('addPackageResource', array(new Reference($packageResourceId)));

        return $definition;
    }

    protected function loadBundlesInformation($bundles, ContainerBuilder $container)
    {
        $bowerManager = $container->getDefinition('sp_bower.bower_manager');
        $filesystem = new Filesystem();

        foreach ($bundles as $bundleName => $bundleConfig) {
            $bundle = $this->getBundleReflectionClass($container, $bundleName);
            if (null === $bundle) {
                throw new \InvalidArgumentException(sprintf('Bundle "%s" does not exist or it is not enabled.', $bundleName));
            }

            $bundleDir = dirname($bundle->getFilename());

            $bundleConfig['config_dir'] = $this->parseDirectory($container, $bundleConfig['config_dir']);
            if (!$filesystem->isAbsolutePath($bundleConfig['config_dir'])) {
                $bundleConfig['config_dir'] = $bundleDir.DIRECTORY_SEPARATOR.$bundleConfig['config_dir'];
            }

            $bundleConfig['asset_dir'] = $this->parseDirectory($container, $bundleConfig['asset_dir']);
            if (!$filesystem->isAbsolutePath($bundleConfig['asset_dir'])) {
                $bundleConfig['asset_dir'] = $bundleConfig['config_dir'].DIRECTORY_SEPARATOR.$bundleConfig['asset_dir'];
            }

            $configuration = new Definition('%sp_bower.bower.configuration.class%');
            $configuration->addArgument($bundleConfig['config_dir']);
            $configuration->addMethodCall('setAssetDirectory', array($bundleConfig['asset_dir']));
            $configuration->addMethodCall('setJsonFile', array($bundleConfig['json_file']));
            $configuration->addMethodCall('setEndpoint', array($bundleConfig['endpoint']));
            $bowerManager->addMethodCall('addBundle', array($bundleName, $configuration));
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string                                                  $bundleName
     *
     * @return null|\ReflectionClass
     */
    private function getBundleReflectionClass(ContainerBuilder $container, $bundleName)
    {
        foreach ($container->getParameter('kernel.bundles') as $name => $class) {
            if ($bundleName === $name) {
                return new \ReflectionClass($class);
            }
        }

        return null;
    }

    /**
     * Convert possible bundle notations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string                                                  $directory
     *
     * @return string
     */
    private function parseDirectory(ContainerBuilder $container, $directory)
    {
        // expand bundle notation
        if ('@' == $directory[0] && false !== strpos($directory, '/')) {
            // use the bundle path as this asset's root
            $bundleName = substr($directory, 1);
            if (false !== $pos = strpos($bundleName, '/')) {
                $bundleName = substr($bundleName, 0, $pos);
            }

            $bundlePath = dirname($this->getBundleReflectionClass($container, $bundleName)->getFileName());
            $directory = str_replace('@'. $bundleName, $bundlePath, $directory);
        }

        return $directory;
    }
}
