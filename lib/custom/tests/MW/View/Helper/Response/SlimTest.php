<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018
 */


namespace Aimeos\MW\View\Helper\Response;


class SlimTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		if( !class_exists( '\Slim\Http\Stream' ) ) {
			$this->markTestSkipped( '\Slim\Http\Stream is not available' );
		}

		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();

		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Response\Slim( $view, $response );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testTransform()
	{
		$this->assertInstanceOf( \Aimeos\MW\View\Helper\Response\Slim::class, $this->object->transform() );
		$this->assertInstanceOf( \Psr\Http\Message\ResponseInterface::class, $this->object->transform() );
	}


	public function testCreateStream()
	{
		$stream = $this->object->createStream( fopen( __FILE__, 'r' ) );

		$this->assertInstanceOf( \Slim\Http\Stream::class, $stream );
		$this->assertInstanceOf( \Psr\Http\Message\StreamInterface::class, $stream );
	}


	public function testCreateStreamFilename()
	{
		$this->assertInstanceOf( \Psr\Http\Message\StreamInterface::class, $this->object->createStream( __FILE__ ) );
	}


	public function testCreateStreamInvalid()
	{
		$this->setExpectedException( \Exception::class );
		$this->object->createStream( -1 );
	}
}
