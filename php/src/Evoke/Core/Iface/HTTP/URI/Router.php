<?php
namespace Evoke\Core\Iface\HTTP\URI;

interface Router
{
	/// Append a URI mapper to the router rules.
	public function appendMapper(Mapper $map);

	/// Create a response from the URI and the mapping rules.
	public function createResponse();

	/// Prepend a URI mapper to the router rules.
	public function prependMapper(Mapper $map);
}
// EOF