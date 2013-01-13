<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Bower;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * The config directory.
     *
     * @var string
     */
    protected $directory;

    /**
     * The directory where the bower dependencies should be installed to.
     *
     * @var string
     */
    protected $assetDirectory;

    /**
     * The name of the json file.
     *
     * @var string
     */
    protected $jsonFile;

    /**
     * The bower endpoint.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Construct.
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param string $assetDirectory
     */
    public function setAssetDirectory($assetDirectory)
    {
        $this->assetDirectory = $assetDirectory;
    }

    /**
     * @return string
     */
    public function getAssetDirectory()
    {
        return $this->assetDirectory;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $json
     */
    public function setJsonFile($json)
    {
        $this->jsonFile = $json;
    }

    /**
     * @return string
     */
    public function getJsonFile()
    {
        return $this->jsonFile;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        $assetDirectory = $this->getAssetDirectory();
        if (null !== $assetDirectory) {
            $filesystem = new Filesystem();
            $assetDirectory = $filesystem->makePathRelative($this->getAssetDirectory(), $this->getDirectory());
        }
        $configuration = array(
            'directory' => $assetDirectory,
            'json' => $this->getJsonFile(),
            'endpoint' => $this->getEndpoint()
        );

        $configuration = array_filter($configuration, function($value) {
            return $value !== null;
        });

        return json_encode($configuration, JSON_FORCE_OBJECT);
    }
}
