<?php

/*
 * This file is part of the Sp/BowerBundle.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Tests\Bower;

use Sp\BowerBundle\Bower\Bower;

/**
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class BowerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bower
     */
    protected $bower;

    protected function setUp()
    {
        $this->bower = new Bower('/usr/bin/bower');
    }

    /**
     * @covers Sp\BowerBundle\Bower\Bower::install
     */
    public function testInstall()
    {
        $output = $this->createOutputMock();
        $output->expects($this->any(6))->method('write');

        $src = __DIR__ .'/Fixtures';
        $target = sys_get_temp_dir() .'/test_target';
        if (!file_exists($target)) {
            mkdir($target);
        }

        $this->bower->install($src .'/component.json', $target, $output);

        $this->assertFileExists($target .'/components');
    }

    private function createOutputMock()
    {
        return $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
    }
}
