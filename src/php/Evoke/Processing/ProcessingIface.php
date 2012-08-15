<?php
/**
 * Processing Interface
 *
 * @package Processing
 */
namespace Evoke\Processing;

/**
 * Processing Interface
 */
interface ProcessingIface
{
	/**
	 * Get the request that is being processed.
	 */
	public function getRequest();

	/**
	 * Process the request.
	 */
	public function process();
}
// EOF