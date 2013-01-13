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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerManager
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $bundles;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->bundles = new ArrayCollection();
    }

    /**
     * @param string                 $bundle
     * @param ConfigurationInterface $configuration
     */
    public function addBundle($bundle, ConfigurationInterface $configuration)
    {
        $this->bundles->set($bundle, $configuration);
    }

    /**
     * @return ArrayCollection
     */
    public function getBundles()
    {
        return $this->bundles;
    }

}
