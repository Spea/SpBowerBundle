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

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class Configuration
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $jsonFile;

    /**
     * @var string
     */
    protected $endpoint;

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
     * @param $endpoint
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
     * @param $json
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

    public function getJson()
    {
        $configuration = array(
            'directory' => $this->getDirectory(),
            'json' => $this->getJsonFile(),
            'endpoint' => $this->getEndpoint()
        );

        $configuration = array_filter($configuration, function($value) {
            return $value !== null;
        });

        return json_encode($configuration, JSON_FORCE_OBJECT);
    }
}
