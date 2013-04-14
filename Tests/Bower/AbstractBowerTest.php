<?php


namespace Sp\BowerBundle\Tests\Bower;


class AbstractBowerTest extends \PHPUnit_Framework_TestCase
{
    public static $fixturesDirectory;

    public static function setUpBeforeClass()
    {
        self::$fixturesDirectory = __DIR__ .'/Fixtures';
    }
}
