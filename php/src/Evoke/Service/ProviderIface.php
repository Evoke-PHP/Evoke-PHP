<?php
namespace Evoke\Service;

/**
 * ProviderIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
interface ProviderIface
{
	/**
	 * Make an object and return it.
	 *
	 * This is the way to create objects (or retrieve shared services) using
	 * Evoke.  Using this method decouples object creation from your code.
	 * This makes it easy to test your code as it is not tightly bound to the
	 * objects that it depends on.
	 *
	 * @param string  Classname, including namespace.
	 * @param mixed[] Construction parameters.  Only the parameters that cannot
	 *                be lazy loaded (scalars with no default or interfaces that
	 *                have no corresponding concrete object with the mapped
	 *                classname) need to be passed.
	 *
	 * @return mixed The object that has been created.
	 */
	public function make($className, Array $params=array());
}
// EOF