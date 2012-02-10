<?php
namespace Evoke\Core\Iface;

interface Session
{
	/// Ensure the session is started.
	public function ensure();

	/// Get the id for the session.
	public function getID();  
}
// EOF