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
interface ValidationIface
{
	/**
	 * Get Failures for the last operation.
	 *
	 * @return Evoke\Message\TreeIface Failure(s) from the last operation.
	 */
	public function getFailures();   

	/**
	 * Return whether the fieldset is valid.
	 *
	 * @param mixed[] The fieldset to check.
	 * @param mixed[] Any fields that should be ignored in the calculation of
	 *                the validity.
	 *
	 * @return bool   Whether the fieldset is valid.
	 */
	public function isValid($fieldset, $ignoredFields=array());
}
// EOF
