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
use Doctrine\Common\Cache\Cache;

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
     * @var null|Cache
     */
    protected $cache;

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
     * {@inheritdoc}
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssetDirectory($assetDirectory)
    {
        $this->assetDirectory = $assetDirectory;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssetDirectory()
    {
        return $this->assetDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * {@inheritdoc}
     */
    public function setJsonFile($json)
    {
        $this->jsonFile = $json;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonFile()
    {
        return $this->jsonFile;
    }

    /**
     * {@inheritdoc}
     */
    public function getJson()
    {
        $assetDirectory = $this->getAssetDirectory();
        if (null !== $assetDirectory) {
            $filesystem = new Filesystem();
            $directory = realpath($this->getDirectory());
            $assetDirectory = $filesystem->makePathRelative($this->getAssetDirectory(), $directory);
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

    /**
     * {@inheritdoc}
     */
    public function setCache(Cache $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->cache;
    }
}
