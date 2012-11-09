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
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;

/**
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
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

    public function setUp()
    {
        if (!isset($_SERVER['BOWER_BIN'])) {
            $this->markTestSkipped('There is no SASS_BIN environment variable.');
        }

        $this->bower = new Bower($_SERVER['BOWER_BIN'], new EventDispatcher());
        $this->target = sys_get_temp_dir() .'/bower_install';
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
     */
    public function testInstall()
    {
        $src = __DIR__ .'/Fixtures';

        $this->bower->install(new DirectoryResource($src), new DirectoryResource($this->target));

        $this->assertFileExists($this->target .'/components');
    }

    public function testPackageInstall()
    {
        $this->bower->install('jquery', new DirectoryResource($this->target));

        $this->assertFileExists($this->target .'/components');
    }
}
