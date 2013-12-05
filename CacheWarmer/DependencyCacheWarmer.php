<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\CacheWarmer;

use Sp\BowerBundle\Bower\Bower;
use Sp\BowerBundle\Bower\BowerManager;
use Sp\BowerBundle\Bower\Exception\InvalidMappingException;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class DependencyCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var \Sp\BowerBundle\Bower\Bower
     */
    private $bower;

    /**
     * @var \Sp\BowerBundle\Bower\BowerManager
     */
    private $bowerManager;

    /**
     * @var bool
     */
    private $install;

    /**
     * @param \Sp\BowerBundle\Bower\BowerManager $bowerManager
     * @param \Sp\BowerBundle\Bower\Bower        $bower
     * @param bool                               $install
     */
    public function __construct(BowerManager $bowerManager, Bower $bower, $install = false)
    {
        $this->bowerManager = $bowerManager;
        $this->bower = $bower;
        $this->install = $install;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        if (!$this->install) {
            return;
        }

        foreach ($this->bowerManager->getBundles() as $config) {
            $this->bower->install($config);
        }
    }
}
