<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Url;


/**
 * View helper class for generating URLs using the Slim router
 *
 * @package MW
 * @subpackage View
 */
class Slim
	extends \Aimeos\MW\View\Helper\Url\Base
	implements \Aimeos\MW\View\Helper\Url\Iface
{
	private $router;
	private $fixed;


	/**
	 * Initializes the URL view helper
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param \Slim\Router $router Slim router object
	 * @param array Associative list of fixed parameters that should be available for all routes
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, \Slim\Router $router, array $fixed )
	{
		parent::__construct( $view );

		$this->router = $router;
		$this->fixed = $fixed;
	}


	/**
	 * Returns the URL assembled from the given arguments.
	 *
	 * @param string|null $target Route or page which should be the target of the link (if any)
	 * @param string|null $controller Name of the controller which should be part of the link (if any)
	 * @param string|null $action Name of the action which should be part of the link (if any)
	 * @param array $params Associative list of parameters that should be part of the URL
	 * @param array $trailing Trailing URL parts that are not relevant to identify the resource (for pretty URLs)
	 * @param array $config Additional configuration parameter per URL
	 * @return string Complete URL that can be used in the template
	 */
	public function transform( string $target = null, string $controller = null, string $action = null,
		array $params = [], array $trailing = [], array $config = [] ) : string
	{
		$params = $this->sanitize( $params );

		if( isset( $config['absoluteUri'] ) && (bool) $config['absoluteUri'] === true ) {
			return $this->router->pathFor( $target, $this->fixed + $params );
		}

		return $this->router->relativePathFor( $target, $params + $this->fixed );
	}
}
