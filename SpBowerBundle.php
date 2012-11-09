<?php

/*
 * This file is part of the Sp/BowerBundle.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Sp\BowerBundle\DependencyInjection\Compiler\ComponentPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SpBowerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ComponentPass());
    }

}
