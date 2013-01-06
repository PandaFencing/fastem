<?php
class HelloWorldTest extends PHPUnit_Framework_TestCase 
{
	public function testHelloWorld() {
		$hello = '127';
		$this->assertEquals('127', $hello);
	}
}
