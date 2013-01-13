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
interface ConfigurationInterface
{

    /**
     * @param string $directory
     * @return void
     */
    public function setDirectory($directory);

    /**
     * @return string
     */
    public function getDirectory();

    /**
     * @param string $assetDirectory
     * @return void
     */
    public function setAssetDirectory($assetDirectory);

    /**
     * @return string
     */
    public function getAssetDirectory();

    /**
     * @param string $json
     * @return void
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
}
