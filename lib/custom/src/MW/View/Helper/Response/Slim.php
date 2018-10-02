<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Response;

use Psr\Http\Message\StreamInterface;


/**
 * View helper class for setting response data
 *
 * @package MW
 * @subpackage View
 */
class Slim
	extends \Aimeos\MW\View\Helper\Response\Standard
	implements \Aimeos\MW\View\Helper\Response\Iface
{
	/**
	 * Creates a new PSR-7 stream object
	 *
	 * @param string|resource Absolute file path or file descriptor
	 * @return \Psr\Http\Message\StreamInterface Stream object
	 */
	public function createStream( $resource )
	{
		if( is_resource( $resource ) === true ) {
			return new \Slim\Http\Stream( $resource );
		}

		if( ( $fh = @fopen( $resource, 'r' ) ) !== false ) {
			return new \Slim\Http\Stream( $fh );
		}

		throw new \Aimeos\MW\Exception( sprintf( 'Unable to open file "%1$s"', $resource ) );
	}
}
