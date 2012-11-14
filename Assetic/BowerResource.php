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
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
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
        foreach ($this->bowerManager->getPaths() as $configDir => $paths) {
            $mapping = $this->bower->getDependencyMapping($configDir);
            foreach ($mapping as $packageName => $values) {
                $packageName = str_replace('.', '_', $packageName);
                $files = $values['source']['main'];
                if (is_string($files)) {
                    $files = array($files);
                }

                $cssFiles = array();
                $jsFiles = array();
                if (isset($values['dependencies'])) {
                    foreach ($values['dependencies'] as $packageDependency => $value) {
                        $packageDependency = str_replace('.', '_', $packageDependency);
                        $jsFiles[] = '@'. $packageDependency .'_js';
                        $cssFiles[] = '@'. $packageDependency .'_css';
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

                $formulae[$packageName .'_css'] = array($cssFiles, array(), array());
                $formulae[$packageName .'_js'] = array($jsFiles, array(), array());
            }
        }

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
