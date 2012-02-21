<?php
namespace Evoke\Model\Session;

class Clear extends Session
{
	public function __construct(Array $setup)
	{
		parent::__construct($setup);

		$this->EventManager->connect('Post.', array($this, 'doNothing'));
		$this->EventManager->connect('Post.Clear', array($this, 'clear'));
	}

	/******************/
	/* Public Methods */
	/******************/

	public function clear()
	{
		$this->sessionManager->remove();
	}

	public function doNothing()
	{

	}
}
// EOF