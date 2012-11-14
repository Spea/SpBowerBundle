<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Tests\Bower;

use Sp\BowerBundle\Bower\Bower;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Resource\DirectoryResource;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bower
     */
    protected $bower;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    protected $bin = '/usr/local/bin';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $processBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected  $process;

    public function setUp()
    {
        $this->target = sys_get_temp_dir() .'/bower_install';
        $this->bower = $this->getMock('Sp\BowerBundle\Bower\Bower', array('getProcessBuilder'), array($this->bin));
        $this->processBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');
        $this->process = $this->getMockBuilder('Symfony\Component\Process\Process')->disableOriginalConstructor()->getMock();
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->target);
    }

    public function tearDown()
    {
        if ($this->filesystem) {
            $this->filesystem->remove($this->target);
        }
    }

    /**
     * @covers Sp\BowerBundle\Bower\Bower::install
     * @dataProvider componentsProvider
     */
    public function testInstall($configDir)
    {
        $this->bower->expects($this->once())->method('getProcessBuilder')->will($this->returnValue($this->processBuilder));

        $this->processBuilder->expects($this->at(1))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo('install'));
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));

        $this->bower->install($configDir);
    }

    public function componentsProvider()
    {
        return array(
            array(__DIR__ .'/Fixtures'),
            array(new DirectoryResource('test')),
        );
    }
}
