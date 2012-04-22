<?php
namespace Evoke\Model\Session;

class Clear extends Session
{
	/******************/
	/* Public Methods */
	/******************/

	public function clear()
	{
		$this->SessionManager->remove();
	}
}
// EOF