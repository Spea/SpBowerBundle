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

/**
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class BowerEvents
{
    /**
     *  The PRE_INSTALL event occurs before the install command is executed.
     */
    const PRE_INSTALL = 'bower.pre_install';

    /**
     * The POST_INSTALL event occurs after the install command is executed.
     */
    const POST_INSTALL = 'bower.post_install';
}
