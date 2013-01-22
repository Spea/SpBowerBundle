<?php

namespace Sp\BowerBundle\EventListener;

use Sp\BowerBundle\Bower\Bower;
use Sp\BowerBundle\Bower\BowerEvent;
use Sp\BowerBundle\Bower\BowerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class CacheCreateListener implements EventSubscriberInterface
{
    /**
     * @var \Sp\BowerBundle\Bower\Bower
     */
    protected $bower;

    /**
     * @param \Sp\BowerBundle\Bower\Bower $bower
     */
    public function __construct(Bower $bower)
    {
        $this->bower = $bower;
    }

    /**
     * @param \Sp\BowerBundle\Bower\BowerEvent $event
     */
    public function onPostExec(BowerEvent $event)
    {
        if (!in_array('install', $event->getCommands())) {
            return;
        }

        $config = $event->getConfiguration();
        $this->bower->createDependencyMappingCache($config);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            BowerEvents::POST_EXEC => array('onPostExec', 0),
        );
    }

}
