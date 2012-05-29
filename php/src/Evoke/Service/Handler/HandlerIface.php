<?php
namespace Evoke\Service\Handler;

interface HandlerIface
{ 
	/// Register the handler.
	public function register();

	/// Unregister the handler.
	public function unregister();
}
// EOF