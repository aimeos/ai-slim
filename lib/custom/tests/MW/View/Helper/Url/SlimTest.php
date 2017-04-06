<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\View\Helper\Url;


class SlimTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $router;


	protected function setUp()
	{
		if( !class_exists( 'Slim\Router', false ) ) {
			$this->markTestSkipped( '\Slim\Router is not available' );
		}

		$view = new \Aimeos\MW\View\Standard();
		$this->router = new \Aimeos\Slim\Router();
		$this->object = new \Aimeos\MW\View\Helper\Url\Slim( $view, $this->router, array( 'site' => 'default' ) );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->router );
	}


	public function testTransform()
	{
		$this->router->map( array( 'GET' ), 'shop/{site}', null )->setName( 'route' );
		$result = $this->object->transform( 'route', 'catalog', 'lists', array( 'key' => 'value' ) );

		$this->assertEquals( 'shop/default?key=value', $result );
	}


	public function testTransformAbsolute()
	{
		$config = array( 'absoluteUri' => true );

		$this->router->setBasePath( 'https://localhost/' );
		$this->router->map( array( 'GET' ), 'shop/{site}', null )->setName( 'route' );

		$result = $this->object->transform( 'route', 'catalog', 'lists', array( 'key' => 'value' ), [], $config );

		$this->assertEquals( 'https://localhost/shop/default?key=value', $result );
	}
}
