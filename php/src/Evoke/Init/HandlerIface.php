<?php
namespace Evoke\Init;

interface HandlerIface
{ 
	/// Register the handler.
	public function register();

	/// Unregister the handler.
	public function unregister();
}
// EOF