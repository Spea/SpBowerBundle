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

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerEvent extends Event
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $commands;

    /**
     * @param ConfigurationInterface $configuration
     * @param array                  $commands
     */
    public function __construct(ConfigurationInterface $configuration, array $commands)
    {
        $this->configuration = $configuration;
        $this->commands = $commands;
    }

    /**
     * @param \Sp\BowerBundle\Bower\ConfigurationInterface $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return \Sp\BowerBundle\Bower\ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }
}
