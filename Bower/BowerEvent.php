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

/**
 * Date: 09.11.12
 * Time: 02:16
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class BowerEvent extends Event
{

    protected $source;

    protected $target;
}
