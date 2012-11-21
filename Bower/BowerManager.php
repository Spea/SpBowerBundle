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
     * @var Bower
     */
    protected $bower;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $bundles;

    /**
     * @param Bower $bower
     */
    public function __construct(Bower $bower)
    {
        $this->bower = $bower;
        $this->bundles = new ArrayCollection();
    }

    /**
     * @param string        $bundle
     * @param Configuration $configuration
     */
    public function addBundle($bundle, Configuration $configuration)
    {
        $this->bundles->set($bundle, $configuration);
    }


    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBundles()
    {
        return $this->bundles;
    }

}
