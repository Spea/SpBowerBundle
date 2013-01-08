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
     */
    function setDirectory($directory);

    /**
     * @return string
     */
    function getDirectory();

    /**
     * @param string $assetDirectory
     */
    function setAssetDirectory($assetDirectory);

    /**
     * @return string
     */
    function getAssetDirectory();

    /**
     * @param string $json
     */
    function setJsonFile($json);

    /**
     * @return string
     */
    function getJsonFile();

    /**
     * @return string
     */
    function getJson();
}
