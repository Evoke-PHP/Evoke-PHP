<?php
namespace Evoke\Model\Session;

class Clear extends \Evoke\Model\Session
{
	/******************/
	/* Public Methods */
	/******************/

	public function clear()
	{
		$this->sessionManager->remove();
	}
}
// EOF