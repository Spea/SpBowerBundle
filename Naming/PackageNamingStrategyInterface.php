<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Naming;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
interface PackageNamingStrategyInterface
{
    /**
     * Translates a given package name to its assetic name.
     *
     * @param $packageName
     *
     * @return string
     */
    public function translateName($packageName);
}
