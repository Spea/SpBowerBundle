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
use Doctrine\Common\Cache\Cache;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
interface ConfigurationInterface
{
    /**
     * @param string $directory
     *
     * @return self
     */
    public function setDirectory($directory);

    /**
     * @return string
     */
    public function getDirectory();

    /**
     * @param string $assetDirectory
     *
     * @return self
     */
    public function setAssetDirectory($assetDirectory);

    /**
     * @return string
     */
    public function getAssetDirectory();

    /**
     * @param string $json
     *
     * @return self
     */
    public function setJsonFile($json);

    /**
     * @return string
     */
    public function getJsonFile();

    /**
     * @return string
     */
    public function getJson();

    /**
     * @param Cache $cache
     */
    public function setCache(Cache $cache);

    /**
     * @return null|Cache
     */
    public function getCache();
}
