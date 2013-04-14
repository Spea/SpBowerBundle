<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Bower\Package;

use Sp\BowerBundle\Bower\ConfigurationInterface;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
interface DependencyMapperInterface
{
    /**
     * @param array                                        $packagesInfo
     * @param \Sp\BowerBundle\Bower\ConfigurationInterface $config
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function map(array $packagesInfo, ConfigurationInterface $config);
}
