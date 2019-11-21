<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018
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
		$route = $request->getAttribute( 'route' );

		parent::__construct( $view, $request, $this->getIPAddress( $request ), ( $route ? $route->getName() : null ) );
	}


	/**
	 * Returns the client IP address
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request
	 * @return string|null IPv4 or IPv6 address or null if invalid
	 */
	protected function getIPAddress( \Psr\Http\Message\ServerRequestInterface $request ) : ?string
	{
		$server = $request->getServerParams();
		$flags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6;

		if( isset( $server['REMOTE_ADDR'] )
			&& ( $ip = filter_var( $server['REMOTE_ADDR'], FILTER_VALIDATE_IP, $flags ) ) !== false
		) {
			return $ip;
		}

		return null;
	}
}
