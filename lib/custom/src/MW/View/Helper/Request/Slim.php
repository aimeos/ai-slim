<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Request;


/**
 * View helper class for accessing request data from Slim
 *
 * @package MW
 * @subpackage View
 */
class Slim
	extends \Aimeos\MW\View\Helper\Request\Standard
	implements \Aimeos\MW\View\Helper\Request\Iface
{
	/**
	 * Initializes the request view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param \Psr\Http\Message\ServerRequestInterface $request PSR-7 request object
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, \Psr\Http\Message\ServerRequestInterface $request )
	{
		$ip = $request->getAttribute( 'ip_address' );
		$route = $request->getAttribute( 'route' )->getName();

		parent::__construct( $view, $request, $ip, $route );
	}
}
