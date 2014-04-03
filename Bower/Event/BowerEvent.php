<?php

namespace Sp\BowerBundle\Bower\Event;

use Sp\BowerBundle\Bower\ConfigurationInterface;
use Symfony\Component\EventDispatcher\Event;

class BowerEvent extends Event
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
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
}
