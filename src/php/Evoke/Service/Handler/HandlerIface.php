<?php
namespace Evoke\Service\Handler;

/**
 * HandlerIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
interface HandlerIface
{
	/**
	 * Register the handler.
	 */
	public function register();

	/**
	 * Unregister the handler.
	 */
	public function unregister();
}
// EOF