<?php
namespace Evoke\Service;

/**
 * ValiditionIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
interface ValiditionIface
{
	/**
	 * Return whether the fieldset is valid.
	 *
	 * @param mixed[] The fieldset to check.
	 *
	 * @return bool Whether the fieldset is valid.
	 */
	public function isValid($fieldset);

	/**
	 * Get Failures for the last operation.
	 *
	 * @return Evoke\Message\TreeIface Failure from the last operation.
	 */
	public function getFailures();   
}
// EOF
