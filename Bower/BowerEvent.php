<?php

/*
 * This file is part of the Sp/BowerBundle.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Bower;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Config\Resource\DirectoryResource;

/**
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class BowerEvent extends Event
{
    /**
     * @var string|\Symfony\Component\Config\Resource\DirectoryResource
     */
    protected $source;

    /**
     * @var \Symfony\Component\Config\Resource\DirectoryResource
     */
    protected $target;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string|\Symfony\Component\Config\Resource\DirectoryResource $source
     * @param \Symfony\Component\Config\Resource\DirectoryResource        $target
     * @param string                                                      $type
     */
    public function __construct($source, DirectoryResource $target, $type)
    {
        $this->source = $source;
        $this->target = $target;
        $this->type = $type;
    }

    /**
     * @return string|\Symfony\Component\Config\Resource\DirectoryResource
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return \Symfony\Component\Config\Resource\DirectoryResource
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
