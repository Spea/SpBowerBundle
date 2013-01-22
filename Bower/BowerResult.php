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

use Symfony\Component\Process\Process;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerResult
{
    /**
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * @var ConfigurationInterface
     */
    protected $config;

    /**
     * @param \Symfony\Component\Process\Process $process
     * @param ConfigurationInterface             $config
     */
    public function __construct(Process $process, ConfigurationInterface $config)
    {
        $this->config = $config;
        $this->process = $process;
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }
}
