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
use Sp\BowerBundle\Bower\Configuration;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
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
        $this->bower = new Bower($_SERVER['BOWER_BIN']);
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->target);
    }

    public function tearDown()
    {
        if ($this->filesystem) {
            $this->filesystem->remove($this->target);
        }
    }

    public function testFileInstall()
    {
        $configuration = new Configuration();
        $configuration->setJsonFile('component.json');
        $configuration->setEndpoint('https://bower.herokuapp.com');
        $src = __DIR__ .'/Fixtures';
        $configuration->setDirectory($this->filesystem->makePathRelative($this->target .'/components', $src));
        $this->bower->init($src, $configuration);
        $this->bower->install($src);

        $this->assertFileExists($this->target .'/components');
        $this->assertFileExists($this->target .'/components/jquery');
        $this->assertFileExists($this->target .'/components/jquery/jquery.js');
    }
}
