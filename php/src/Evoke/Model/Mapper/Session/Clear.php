<?php
namespace Evoke\Model\Mapper\Session;

class Clear extends Session
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