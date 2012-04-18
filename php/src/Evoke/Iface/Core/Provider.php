<?php
namespace Evoke\Iface\Core;

interface Provider
{
	/** Make an object and return it.
	 *
	 *  This is the way to create objects (or retrieve shared services) using
	 *  Evoke.  Using this method decouples object creation from your code.
	 *  This makes it easy to test your code as it is not tightly bound to the
	 *  objects that it depends on.
	 *
	 *  @param className \string Classname, including namespace.
	 *  @param \array  params    Construction parameters.  Only the parameters
	 *  that cannot be lazy loaded (scalars with no default or interfaces that
	 *  have no corresponding concrete object with the mapped classname) need to
	 *  be passed.
	 *
	 *  @return The object that has been created.
	 */
	public function make($className, Array $params=array());

	/** Set the specified class to be shared by the Provider.  The make method
	 *  will return a shared object for this class while the class remains
	 *  shared.
	 *  @param className \string  Classname (including namespace).
	 */
	public function share($className);

	/** Stop the class from being shared by the Provider, forcing a new object
	 *  to be created for the class each time it is made using make.
	 *  @param className \string The classname to unshare.
	 */
	public function unshare($className);
}
// EOF