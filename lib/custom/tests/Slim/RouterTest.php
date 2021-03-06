<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/master/LICENSE.md (MIT License)
 */


class RouterTest extends \PHPUnit\Framework\TestCase
{
	/** @var Router */
	protected $router;

	public function setUp() : void
	{
		if( !class_exists( \Slim\Router::class, false ) ) {
			$this->markTestSkipped( '\Slim\Router is not available' );
		}

		$this->router = new \Aimeos\Slim\Router;
	}

	public function testRelativePathFor()
	{
		$this->router->setBasePath('/base/path');

		$methods = ['GET'];
		$pattern = '/hello/{first:\w+}/{last}';
		$callable = function ( $request, $response, $args ) {
			echo sprintf('Hello %s %s', $args['first'], $args['last']);
		};
		$route = $this->router->map($methods, $pattern, $callable);
		$route->setName('foo');

		$this->assertEquals(
			'/hello/josh/lockhart',
			$this->router->relativePathFor('foo', ['first' => 'josh', 'last' => 'lockhart'])
		);
	}

	public function testPathForWithNoBasePath()
	{
		$this->router->setBasePath('');

		$methods = ['GET'];
		$pattern = '/hello/{first:\w+}/{last}';
		$callable = function ( $request, $response, $args ) {
			echo sprintf('Hello %s %s', $args['first'], $args['last']);
		};
		$route = $this->router->map($methods, $pattern, $callable);
		$route->setName('foo');

		$this->assertEquals(
			'/hello/josh/lockhart',
			$this->router->pathFor('foo', ['first' => 'josh', 'last' => 'lockhart'])
		);
	}

	public function testPathForWithBasePath()
	{
		$methods = ['GET'];
		$pattern = '/hello/{first:\w+}/{last}';
		$callable = function ( $request, $response, $args ) {
			echo sprintf('Hello %s %s', $args['first'], $args['last']);
		};
		$this->router->setBasePath('/base/path');
		$route = $this->router->map($methods, $pattern, $callable);
		$route->setName('foo');

		$this->assertEquals(
			'/base/path/hello/josh/lockhart',
			$this->router->pathFor('foo', ['first' => 'josh', 'last' => 'lockhart'])
		);
	}

	public function testPathForWithOptionalParameters()
	{
		$methods = ['GET'];
		$pattern = '/archive/{year}[/{month:[\d:{2}]}[/d/{day}]]';
		$callable = function ( $request, $response, $args ) {
			return $response;
		};
		$route = $this->router->map($methods, $pattern, $callable);
		$route->setName('foo');

		$this->assertEquals(
			'/archive/2015',
			$this->router->pathFor('foo', ['year' => '2015'])
		);
		$this->assertEquals(
			'/archive/2015/07',
			$this->router->pathFor('foo', ['year' => '2015', 'month' => '07'])
		);
		$this->assertEquals(
			'/archive/2015/07/d/19',
			$this->router->pathFor('foo', ['year' => '2015', 'month' => '07', 'day' => '19'])
		);
	}

	public function testPathForWithSurplusRouteParameters()
	{
		$methods = ['GET'];
		$pattern = '/hello/{name}';
		$callable = function ( $request, $response, $args ) {
			echo sprintf('Hello %s', $args['name']);
		};
		$route = $this->router->map($methods, $pattern, $callable);
		$route->setName('foo');

		$this->assertEquals(
			'/hello/josh?a=b',
			$this->router->pathFor('foo', ['name' => 'josh', 'a' => 'b'])
		);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testPathForWithMissingSegmentData()
	{
		$methods = ['GET'];
		$pattern = '/hello/{first}/{last}';
		$callable = function ( $request, $response, $args ) {
			echo sprintf('Hello %s %s', $args['first'], $args['last']);
		};
		$route = $this->router->map($methods, $pattern, $callable);
		$route->setName('foo');

		$this->router->pathFor('foo', ['last' => 'lockhart']);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testPathForRouteNotExists()
	{
		$methods = ['GET'];
		$pattern = '/hello/{first}/{last}';
		$callable = function ( $request, $response, $args ) {
			echo sprintf('Hello %s %s', $args['first'], $args['last']);
		};
		$route = $this->router->map($methods, $pattern, $callable);
		$route->setName('foo');

		$this->router->pathFor('bar', ['first' => 'josh', 'last' => 'lockhart']);
	}
}
