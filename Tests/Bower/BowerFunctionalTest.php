<?php
namespace Sp\BowerBundle\Tests\Bower;

use Sp\BowerBundle\Bower\Bower;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Date: 10.11.12
 * Time: 17:39
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class BowerFunctionalTest extends \PHPUnit_Framework_TestCase
{
    protected  $bower;
    protected  $target;
    protected  $filesystem;

    public function setUp()
    {
        if (!isset($_SERVER['BOWER_BIN'])) {
            $this->markTestSkipped('There is no BOWER_BIN environment variable.');
        }

        $this->target = sys_get_temp_dir() .'/bower_install';
        $this->bower = new Bower($_SERVER['BOWER_BIN'], new EventDispatcher());
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->target);
    }

    public function tearDown()
    {
        if ($this->filesystem) {
            $this->filesystem->remove($this->target);
        }
    }

    public function testPackageInstall()
    {
        $this->bower->install('backbone', new DirectoryResource($this->target));

        $this->assertFileExists($this->target .'/components');
        $this->assertFileExists($this->target .'/components/backbone');
        $this->assertFileExists($this->target .'/components/backbone/backbone.js');
    }

    public function testFileInstall()
    {
        $src = __DIR__ .'/Fixtures';
        $this->bower->install(new DirectoryResource($src), new DirectoryResource($this->target));

        $this->assertFileExists($this->target .'/components');
        $this->assertFileExists($this->target .'/components/jquery');
        $this->assertFileExists($this->target .'/components/jquery/jquery.js');
    }
}
