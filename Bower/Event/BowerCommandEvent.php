<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Bower\Event;

use Sp\BowerBundle\Bower\ConfigurationInterface;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerCommandEvent extends BowerEvent
{
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
        parent::__construct($configuration);
        $this->commands = $commands;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }
}
