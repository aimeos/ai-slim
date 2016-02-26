<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\View\Helper\Request;


class SlimTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $mock;


	protected function setUp()
	{
		if( !interface_exists( '\Psr\Http\Message\ServerRequestInterface' ) ) {
			$this->markTestSkipped( '\Psr\Http\Message\ServerRequestInterface is not available' );
		}

		if( !class_exists( '\Slim\Route' ) ) {
			$this->markTestSkipped( '\Slim\Route is not available' );
		}

		$view = new \Aimeos\MW\View\Standard();
		$route = new \Slim\Route( array( 'GET' ), '/shop', null );
		$route->setName( 'route' );

		$this->mock = $this->getMock( '\Psr\Http\Message\ServerRequestInterface' );

		$this->mock->expects( $this->exactly( 2 ) )->method( 'getAttribute' )
			->will( $this->onConsecutiveCalls( '127.0.0.1', $route ) );

		$this->object = new \Aimeos\MW\View\Helper\Request\Slim( $view, $this->mock );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->mock );
	}


	public function testTransform()
	{
		$this->assertInstanceOf( '\Aimeos\MW\View\Helper\Request\Slim', $this->object->transform() );
		$this->assertInstanceOf( '\Psr\Http\Message\ServerRequestInterface', $this->object->transform() );
	}


	public function testGetClientAddress()
	{
		$this->assertEquals( '127.0.0.1', $this->object->getClientAddress() );
	}


	public function testGetTarget()
	{
		$this->assertEquals( 'route', $this->object->getTarget() );
	}
}
