<?php
namespace Evoke\Model\Mapper\Session;

class Clear extends \Evoke\Model\Mapper\Session
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