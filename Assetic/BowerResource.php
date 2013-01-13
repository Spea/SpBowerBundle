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

use Symfony\Bundle\AsseticBundle\Factory\Resource\ConfigurationResource;
use Sp\BowerBundle\Bower\Exception;
use Sp\BowerBundle\Bower\BowerManager;
use Sp\BowerBundle\Bower\Bower;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerResource extends ConfigurationResource
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
     * @param \Sp\BowerBundle\Bower\Bower        $bower
     * @param \Sp\BowerBundle\Bower\BowerManager $bowerManager
     */
    public function __construct(Bower $bower, BowerManager $bowerManager)
    {
        $this->bower = $bower;
        $this->bowerManager = $bowerManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        $formulae = array();
        foreach ($this->bowerManager->getBundles() as $config) {
            try {
                $mapping = $this->bower->getDependencyMapping($config);
            } catch (Exception $ex) {
                throw new Exception('Dependency cache keys not yet generated, run "app/console sp:bower:install" to initiate the cache' . $ex->getMessage());
            }

            foreach ($mapping as $packageName => $package) {
                $packageName = $this->convertPackageName($packageName);
                $formulae = array_merge($this->createPackageFormulae($package, $packageName, $config->getDirectory()), $formulae);
            }
        }

        return $formulae;
    }

    /**
     * Creates formulae for the given package.
     *
     * @param array  $package
     * @param string $packageName
     * @param string $configDir
     *
     * @return array<string,array<array>>
     */
    protected function createPackageFormulae(array $package, $packageName, $configDir)
    {
        $formulae = array();
        $files = array();
        if (isset($package['source']['main'])) {
            $files = $package['source']['main'];
            if (is_string($files)) {
                $files = array($files);
            }
        }

        $cssFiles = array();
        $jsFiles = array();
        if (isset($package['dependencies'])) {
            foreach ($package['dependencies'] as $packageDependency => $value) {
                $packageDependency = $this->convertPackageName($packageDependency);
                $jsFiles[] = '@' . $packageDependency . '_js';
                $cssFiles[] = '@' . $packageDependency . '_css';
            }
        }

        foreach ($files as $file) {
            if ($this->isJavascript($file)) {
                $jsFiles[] = $file;
            }

            if ($this->isStylesheet($file)) {
                $cssFiles[] = $file;
            }
        }

        $cssFiles = $this->resolvePaths($configDir, $cssFiles);
        $jsFiles = $this->resolvePaths($configDir, $jsFiles);

        $formulae[$packageName . '_css'] = array($cssFiles, array(), array());
        $formulae[$packageName . '_js'] = array($jsFiles, array(), array());

        return $formulae;
    }

    public function __toString()
    {
        return 'bower';
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    protected function isJavascript($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION) == 'js';
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    protected function isStylesheet($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION) == 'css';
    }

    /**
     * @param string $packageName
     *
     * @return string
     */
    protected function convertPackageName($packageName)
    {
        return str_replace(array('-', '.'), '_', $packageName);
    }

    /**
     * Creates an absolute path for all passed files based on the config directory..
     *
     * @param string $configDir
     * @param array  $files
     *
     * @return array
     */
    protected function resolvePaths($configDir, array $files = array())
    {
        chdir($configDir);
        foreach ($files as $key => $file) {
            if (strpos($file, '@') === 0) {
                continue;
            }
            $files[$key] = realpath($file);
        }

        return $files;
    }
}
