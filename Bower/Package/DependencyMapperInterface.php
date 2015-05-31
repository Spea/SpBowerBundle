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

use Doctrine\Common\Collections\Collection;
use Sp\BowerBundle\Bower\ConfigurationInterface;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
interface DependencyMapperInterface
{
    /**
     * @param array                  $packagesInfo
     * @param ConfigurationInterface $config
     *
     * @return Collection|Package[]
     */
    public function map(array $packagesInfo, ConfigurationInterface $config);
}
