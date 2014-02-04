<?php
/**
 * Processing Interface
 *
 * @package Service
 */
namespace Evoke\Service;

/**
 * Processing Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service
 */
interface ProcessingIface
{
	/**
	 * Add a callback to the request key's list of callbacks.
	 *
	 * @param string   The request key for the matching.
	 * @param callable The callback that is being added.
	 */
	public function addCallback(/* String */ $requestKey,
	                            callable $callback);
	
	/**
	 * Process the request.
	 */
	public function process();

	/**
	 * Set the data for the request that we are processing.
	 *
	 * @param mixed[] The request data that we are processing.
	 */
	public function setData(Array $data);
}
// EOF