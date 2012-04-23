<?php
namespace Evoke\Iface\Core\Init;

interface Handler
{ 
	/// Register the handler.
	public function register();

	/// Unregister the handler.
	public function unregister();
}
// EOF