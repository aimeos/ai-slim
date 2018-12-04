<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018
 */


namespace Aimeos\MW\View\Helper\Request;


class SlimTest extends \PHPUnit\Framework\TestCase
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

		$this->mock = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();

		$this->mock->expects( $this->once() )->method( 'getAttribute' )
			->will( $this->returnValue( $route ) );

		$this->mock->expects( $this->once() )->method( 'getServerParams' )
			->will( $this->returnValue( array( 'REMOTE_ADDR' => '127.0.0.1' ) ) );

		$this->object = new \Aimeos\MW\View\Helper\Request\Slim( $view, $this->mock );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->mock );
	}


	public function testTransform()
	{
		$this->assertInstanceOf( \Aimeos\MW\View\Helper\Request\Slim::class, $this->object->transform() );
		$this->assertInstanceOf( \Psr\Http\Message\ServerRequestInterface::class, $this->object->transform() );
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
