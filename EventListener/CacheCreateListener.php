<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\EventListener;

use Sp\BowerBundle\Bower\Bower;
use Sp\BowerBundle\Bower\Event\BowerCommandEvent;
use Sp\BowerBundle\Bower\Event\BowerEvent;
use Sp\BowerBundle\Bower\Event\BowerEvents;
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
     * @param \Sp\BowerBundle\Bower\Event\BowerEvent $event
     */
    public function onPostInstall(BowerEvent $event)
    {
        $this->bower->createDependencyMappingCache($event->getConfiguration());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            BowerEvents::POST_INSTALL => array('onPostInstall', 0),
        );
    }

}
