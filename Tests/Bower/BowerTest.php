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
    protected $process;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    public function setUp()
    {
        $this->target = sys_get_temp_dir() .'/bower_install';
        $this->cache = $this->getMock('Doctrine\Common\Cache\Cache');
        $this->bower = new Bower($this->bin, $this->cache);
        $this->processBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');
        $this->bower->setProcessBuilder($this->processBuilder);
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
        $this->processBuilder->expects($this->at(1))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo('install'));
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));

        $this->bower->install($configDir);
    }

    /**
     * @covers Sp\BowerBundle\Bower\Bower::createDependencyMappingCache
     */
    public function testCreateDependencyMappingCache()
    {
        $configDir = "/config_dir";

        $jsonDependencyMapping = file_get_contents(__DIR__ .'/Fixtures/dependency_mapping.json');
        $arrayDependencyMapping = require __DIR__ .'/Fixtures/dependency_mapping.php';

        $this->processBuilder->expects($this->at(1))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo('list'));
        $this->processBuilder->expects($this->at(3))->method('add')->with($this->equalTo('--map'));
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));
        $this->process->expects($this->once())->method('getOutput')->will($this->returnValue($jsonDependencyMapping));

        $this->cache->expects($this->once())->method('save')->with($this->equalTo($configDir), $this->equalTo($arrayDependencyMapping));

        $this->bower->createDependencyMappingCache($configDir);
    }

    public function componentsProvider()
    {
        return array(
            array(__DIR__ .'/Fixtures/config'),
            array(new DirectoryResource('test')),
        );
    }

}
