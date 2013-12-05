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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sp\BowerBundle\Bower\Bower;
use Sp\BowerBundle\Bower\BowerManager;
use Sp\BowerBundle\Bower\ConfigurationInterface;
use Sp\BowerBundle\Bower\Exception\FileNotFoundException;
use Sp\BowerBundle\Bower\Exception\InvalidArgumentException;
use Sp\BowerBundle\Bower\Exception\RuntimeException;
use Sp\BowerBundle\Bower\Package\Package;
use Sp\BowerBundle\Naming\PackageNamingStrategyInterface;
use Symfony\Bundle\AsseticBundle\Factory\Resource\ConfigurationResource;

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
     * @var bool
     */
    protected $nestDependencies = true;

    /**
     * @var array
     */
    protected $cssFilters = array();

    /**
     * @var array
     */
    protected $jsFilters = array();

    /**
     * @var Collection
     */
    protected $packageResources;

    /**
     * @var array
     */
    protected $typeGetters = array(
        'css' => 'getStyles',
        'js'  => 'getScripts'
    );

    /**
     * Constructor
     *
     * @param \Sp\BowerBundle\Bower\Bower                           $bower
     * @param \Sp\BowerBundle\Bower\BowerManager                    $bowerManager
     * @param \Sp\BowerBundle\Naming\PackageNamingStrategyInterface $namingStrategy
     */
    public function __construct(Bower $bower, BowerManager $bowerManager, PackageNamingStrategyInterface $namingStrategy)
    {
        $this->bower = $bower;
        $this->bowerManager = $bowerManager;
        $this->namingStrategy = $namingStrategy;
        $this->packageResources = new ArrayCollection();
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
            } catch (FileNotFoundException $ex) {
                throw $ex;
            } catch (RuntimeException $ex) {
                throw new RuntimeException('Dependency cache keys not yet generated, run "app/console sp:bower:install" to initiate the cache: ' . $ex->getMessage());
            }

            /** @var $package Package */
            foreach ($mapping as $package) {
                $packageName = $this->namingStrategy->translateName($package->getName());

                $packageFormula = $this->createPackageFormulae($package, $packageName, $config->getDirectory());
                if (!empty($packageFormula)) {
                    $formulae = array_merge($packageFormula, $formulae);
                }
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
     * @param PackageResource $packageResource
     *
     * @return $this
     */
    public function addPackageResource(PackageResource $packageResource)
    {
        $this->packageResources->set($packageResource->getName(), $packageResource);

        return $this;
    }

    /**
     * @param boolean $nestDependencies
     */
    public function setNestDependencies($nestDependencies)
    {
        $this->nestDependencies = $nestDependencies;
    }

    /**
     * @return boolean
     */
    public function shouldNestDependencies()
    {
        return $this->nestDependencies;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'bower';
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list($this->cssFilters, $this->jsFilter, $this->packageResources) = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array($this->cssFilters, $this->jsFilters, $this->packageResources));
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

        /** @var PackageResource $packageResource */
        $packageResource = $this->packageResources->get($packageName);

        $nestDependencies = $this->shouldNestDependencies();
        if (null !== $packageResource && null !== $packageResource->shouldNestDependencies()) {
            $nestDependencies = $packageResource->shouldNestDependencies();
        }

        if (null !== $cssFiles = $this->createSingleFormula($package, $nestDependencies, 'css')) {
            $formulae[$packageName . '_css'] = array($cssFiles, $this->resolveCssFilters($packageResource), array());
        }

        if (null !== $jsFiles = $this->createSingleFormula($package, $nestDependencies, 'js')) {
            $formulae[$packageName . '_js'] = array($jsFiles, $this->resolveJsFilters($packageResource), array());
        }

        return $formulae;
    }

    /**
     * @param PackageResource|null $packageResource
     *
     * @return array
     */
    protected function resolveCssFilters(PackageResource $packageResource = null)
    {
        $cssFilters = $this->getCssFilters();
        if (null !== $packageResource) {
            $cssFilters = array_merge($cssFilters, $packageResource->getCssFilters()->toArray());
        }

        return $cssFilters;
    }

    /**
     * @param PackageResource|null $packageResource
     *
     * @return array
     */
    protected function resolveJsFilters(PackageResource $packageResource = null)
    {
        $jsFilters = $this->getJsFilters();
        if (null !== $packageResource) {
            $jsFilters = array_merge($jsFilters, $packageResource->getJsFilters()->toArray());
        }

        return $jsFilters;
    }

    /**
     * Create single formula for package
     *
     * @param Package $package
     * @param Boolean $nestDependencies
     * @param string  $typeExtension
     *
     * @return null
     *
     * @throws InvalidArgumentException
     */
    protected function createSingleFormula(Package $package, $nestDependencies, $typeExtension)
    {
        if (!in_array($typeExtension, array_keys($this->typeGetters))) {
            throw new InvalidArgumentException(
                sprintf(
                    "Extension '%s' is not in list of valid extensions: %s",
                    $typeExtension,
                    implode(', ', array_keys($this->typeGetters))
                )
            );
        }

        $typeGetter = $this->typeGetters[$typeExtension];

        // fetch the files from the package with the specified getter
        $files = $package->{$typeGetter}()->toArray();

        if (empty($files)) {
            return null;
        }

        if ($nestDependencies) {
            /** @var $packageDependency Package */
            foreach ($package->getDependencies() as $packageDependency) {
                $depFiles = $packageDependency->{$typeGetter}()->toArray();
                if (empty($depFiles)) {
                    continue;
                }

                $packageDependencyName = $this->namingStrategy->translateName($packageDependency->getName());
                array_unshift($files, '@' . $packageDependencyName . '_' . $typeExtension);
            }
        }

        return $files;
    }
}
