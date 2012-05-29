<?php
namespace Evoke\Persistance;

interface SessionIface
{
	/// Ensure the session is started.
	public function ensure();

	/// Get the id for the session.
	public function getID();  
}
// EOF