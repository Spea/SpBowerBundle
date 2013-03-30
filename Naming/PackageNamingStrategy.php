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
class PackageNamingStrategy implements PackageNamingStrategyInterface
{

    /**
     * {@inheritdoc}
     */
    public function translateName($packageName)
    {
        return str_replace(array('-', '.'), '_', $packageName);
    }
}
