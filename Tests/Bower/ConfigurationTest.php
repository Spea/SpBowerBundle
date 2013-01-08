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
use Sp\BowerBundle\Bower\Configuration;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Sp\BowerBundle\Bower\Configuration::getJson
     */
    public function testGetJsonReturnsEmptyObject()
    {
        $config = new Configuration('/foo');

        $this->assertEquals('{}', $config->getJson());
    }

    /**
     * @covers Sp\BowerBundle\Bower\Configuration::getJson
     */
    public function testGetJsonReturnsObject()
    {
        $config = new Configuration('/foo');
        $config->setDirectory('/tmp');
        $config->setAssetDirectory('/foo');
        $config->setJsonFile('foo.json');

        $expected = <<<json
{"directory":"..\/foo\/","json":"foo.json"}
json;

        $this->assertEquals($expected, $config->getJson());
    }
}
