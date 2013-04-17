<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Assetic;

use Sp\BowerBundle\Bower\ConfigurationInterface;
use Sp\BowerBundle\Bower\Exception\FileNotFoundException;
use Sp\BowerBundle\Bower\Exception\RuntimeException;
use Sp\BowerBundle\Bower\Package\Package;
use Sp\BowerBundle\Naming\PackageNamingStrategyInterface;
use Symfony\Bundle\AsseticBundle\Factory\Resource\ConfigurationResource;
use Sp\BowerBundle\Bower\BowerManager;
use Sp\BowerBundle\Bower\Bower;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerResource extends ConfigurationResource implements \Serializable
{
    /**
     * @var \Sp\BowerBundle\Bower\Bower
     */
    protected $bower;

    /**
     * @var \Sp\BowerBundle\Bower\BowerManager
     */
    protected $bowerManager;

    /**
     * @var \Sp\BowerBundle\Naming\PackageNamingStrategyInterface
     */
    protected $namingStrategy;

    /**
     * @var array
     */
    protected $cssFilters = array();

    /**
     * @var array
     */
    protected $jsFilters = array();

    /**
     * @var array
     */
    protected $packageCssFilters = array();

    /**
     * @var array
     */
    protected $packageJsFilters = array();

    /**
     * @param \Sp\BowerBundle\Bower\Bower                           $bower
     * @param \Sp\BowerBundle\Bower\BowerManager                    $bowerManager
     * @param \Sp\BowerBundle\Naming\PackageNamingStrategyInterface $namingStrategy
     */
    public function __construct(Bower $bower, BowerManager $bowerManager, PackageNamingStrategyInterface $namingStrategy)
    {
        $this->bower = $bower;
        $this->bowerManager = $bowerManager;
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        $formulae = array();
        /** @var $config ConfigurationInterface */
        foreach ($this->bowerManager->getBundles() as $config) {
            try {
                $mapping = $this->bower->getDependencyMapping($config);
            } catch(FileNotFoundException $ex) {
                throw $ex;
            } catch (RuntimeException $ex) {
                throw new RuntimeException('Dependency cache keys not yet generated, run "app/console sp:bower:install" to initiate the cache: ' . $ex->getMessage());
            }

            /** @var $package Package */
            foreach ($mapping as $package) {
                $packageName = $this->namingStrategy->translateName($package->getName());
                $formulae = array_merge($this->createPackageFormulae($package, $packageName, $config->getDirectory()), $formulae);
            }
        }

        return $formulae;
    }

    /**
     * @param array $cssFilter
     */
    public function setCssFilters(array $cssFilter)
    {
        $this->cssFilters = $cssFilter;
    }

    /**
     * @return array
     */
    public function getCssFilters()
    {
        return $this->cssFilters;
    }

    /**
     * @param array $jsFilter
     */
    public function setJsFilters(array $jsFilter)
    {
        $this->jsFilters = $jsFilter;
    }

    /**
     * @return array
     */
    public function getJsFilters()
    {
        return $this->jsFilters;
    }

    /**
     * @param array $packageCssFilters
     *
     * @return $this
     */
    public function setPackageCssFilters(array $packageCssFilters)
    {
        $this->packageCssFilters = $packageCssFilters;

        return $this;
    }

    /**
     * @param string $packageName
     * @param array  $filters
     *
     * @return $this
     */
    public function addPackageCssFilters($packageName, array $filters)
    {
        $this->packageCssFilters[$packageName] = $filters;

        return $this;
    }

    /**
     * @return array
     */
    public function getPackageCssFilters()
    {
        return $this->packageCssFilters;
    }

    /**
     * @param array $packageJsFilters
     *
     * @return $this
     */
    public function setPackageJsFilters(array $packageJsFilters)
    {
        $this->packageJsFilters = $packageJsFilters;

        return $this;
    }

    /**
     * @param string $packageName
     * @param array  $filters
     *
     * @return $this
     */
    public function addPackageJsFilters($packageName, array $filters)
    {
        $this->packageJsFilters[$packageName] = $filters;

        return $this;
    }

    /**
     * @return array
     */
    public function getPackageJsFilters()
    {
        return $this->packageJsFilters;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'bower';
    }

    /**
     * Creates formulae for the given package.
     *
     * @param \Sp\BowerBundle\Bower\Package\Package $package
     * @param string                                $packageName
     * @param string                                $configDir
     *
     * @return array<string,array<array>>
     */
    protected function createPackageFormulae(Package $package, $packageName, $configDir)
    {
        $formulae = array();

        $cssFiles = $package->getStyles()->toArray();
        $jsFiles = $package->getScripts()->toArray();
        /** @var $packageDependency Package */
        foreach ($package->getDependencies() as $packageDependency) {
            $packageDependencyName = $this->namingStrategy->translateName($packageDependency->getName());
            array_unshift($jsFiles, '@' . $packageDependencyName . '_js');
            array_unshift($cssFiles, '@' . $packageDependencyName . '_css');
        }

        $formulae[$packageName . '_css'] = array($cssFiles, $this->resolveCssFilters($packageName), array());
        $formulae[$packageName . '_js'] = array($jsFiles, $this->resolveJsFilters($packageName), array());

        return $formulae;
    }

    /**
     * @param string $packageName
     *
     * @return array
     */
    protected function resolveCssFilters($packageName)
    {
        $cssFilters = $this->getCssFilters();
        if (isset($this->packageCssFilters[$packageName])) {
            $cssFilters = array_merge($cssFilters, $this->packageCssFilters[$packageName]);
        }

        return $cssFilters;
    }

    /**
     * @param string $packageName
     *
     * @return array
     */
    protected function resolveJsFilters($packageName)
    {
        $jsFilters = $this->getJsFilters();
        if (isset($this->packageJsFilters[$packageName])) {
            $jsFilters = array_merge($jsFilters, $this->packageJsFilters[$packageName]);
        }

        return $jsFilters;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array($this->cssFilters, $this->jsFilters, $this->packageJsFilters, $this->packageCssFilters));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list($this->cssFilters, $this->jsFilter, $this->packageJsFilters, $this->packageCssFilters) = unserialize($serialized);
    }
}
