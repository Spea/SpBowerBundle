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

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
final class BowerEvents
{
    const PRE_EXEC = 'bower.pre_exec';

    const POST_EXEC = 'bower.post_exec';

    const PRE_INSTALL = 'bower.pre_install';

    const POST_INSTALL = 'bower.post_install';
    
    const PRE_UPDATE = 'bower.pre_update';

    const POST_UPDATE = 'bower.post_update';
}
