<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package Slim
 */


namespace Aimeos\Slim;

use InvalidArgumentException;


/**
 * Router
 *
 * This class organizes Slim application route objects. It is responsible
 * for registering route objects, assigning names to route objects,
 * finding routes that match the current HTTP request, and creating
 * URLs for a named route.
 */
class Router extends \Slim\Router
{
	/**
	 * Build the path for a named route excluding the base path
	 *
	 * @param string $name		Route name
	 * @param array  $data		Named argument replacement data
	 * @param array  $queryParams Optional query string parameters
	 *
	 * @return string
	 *
	 * @throws RuntimeException		 If named route does not exist
	 * @throws InvalidArgumentException If required data not provided
	 */
	public function relativePathFor($name, array $data = [], array $queryParams = [])
	{
		$route = $this->getNamedRoute($name);
		$pattern = $route->getPattern();

		$routeDatas = $this->routeParser->parse($pattern);
		// $routeDatas is an array of all possible routes that can be made. There is
		// one routedata for each optional parameter plus one for no optional parameters.
		//
		// The most specific is last, so we look for that first.
		$routeDatas = array_reverse($routeDatas);

		$segments = $segmentKeys = [];
		foreach ($routeDatas as $routeData) {
			foreach ($routeData as $item) {
				if (is_string($item)) {
					// this segment is a static string
					$segments[] = $item;
					continue;
				}

				// This segment has a parameter: first element is the name
				if (!array_key_exists($item[0], $data)) {
					// we don't have a data element for this segment: cancel
					// testing this routeData item, so that we can try a less
					// specific routeData item.
					$segments = [];
					$segmentName = $item[0];
					break;
				}
				$segments[] = $data[$item[0]];
				$segmentKeys[$item[0]] = true;
			}
			if (!empty($segments)) {
				// we found all the parameters for this route data, no need to check
				// less specific ones
				break;
			}
		}

		if (empty($segments)) {
			throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
		}
		$url = implode('', $segments);

		$params = array_merge(array_diff_key($data, $segmentKeys), $queryParams);
		if ($params) {
			$url .= '?' . http_build_query($params);
		}

		return $url;
	}

    /**
     * Pull route info for a request with a bad method to decide whether to
     * return a not-found error (default) or a bad-method error, then run
     * the handler for that error, returning the resulting response.
     *
     * Used for cases where an incoming request has an unrecognized method,
     * rather than throwing an exception and not catching it all the way up.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function processInvalidMethod(ServerRequestInterface $request, ResponseInterface $response)
    {
        $router = $this->container->get('router');
        if (is_callable([$request->getUri(), 'getBaseUrl']) && is_callable([$router, 'getBaseUrl'])) {
            $router->setBasePath($request->getUri()->getBaseUrl());
        }

        $request = $this->dispatchRouterAndPrepareRoute($request, $router);
        $routeInfo = $request->getAttribute('routeInfo', [RouterInterface::DISPATCH_STATUS => Dispatcher::NOT_FOUND]);

        if ($routeInfo[RouterInterface::DISPATCH_STATUS] === Dispatcher::METHOD_NOT_ALLOWED) {
            return $this->handleException(
                new MethodNotAllowedException($request, $response, $routeInfo[RouterInterface::ALLOWED_METHODS]),
                $request,
                $response
            );
        }

        return $this->handleException(new NotFoundException($request, $response), $request, $response);
    }

    /**
     * Process a request
     *
     * This method traverses the application middleware stack and then returns the
     * resultant Response object.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     *
     * @throws Exception
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response)
    {
        // Ensure basePath is set
        $router = $this->container->get('router');
        if (is_callable([$request->getUri(), 'getBaseUrl']) && is_callable([$router, 'getBaseUrl'])) {
            $router->setBasePath($request->getUri()->getBaseUrl());
        }

        // Dispatch the Router first if the setting for this is on
        if ($this->container->get('settings')['determineRouteBeforeAppMiddleware'] === true) {
            // Dispatch router (note: you won't be able to alter routes after this)
            $request = $this->dispatchRouterAndPrepareRoute($request, $router);
        }

        // Traverse middleware stack
        try {
            $response = $this->callMiddlewareStack($request, $response);
        } catch (Exception $e) {
            $response = $this->handleException($e, $request, $response);
        } catch (Throwable $e) {
            $response = $this->handlePhpError($e, $request, $response);
        }

        $response = $this->finalize($response);

        return $response;
    }
}
