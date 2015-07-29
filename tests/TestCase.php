<?php

namespace Lionar\Events\Tests;
	
use Mockery,
    PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }
}
