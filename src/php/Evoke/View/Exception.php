<?php
/**
 * Exception View
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Exception View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Exception implements ViewIface
{
	protected 
		/**
		 * Exception
		 * @var \Exception
		 */
		$exception,
	
		/**
		 * Description
		 * @var string
		 */
		$description,

		/**
		 * Is Detailed
		 * @var bool
		 */
		$isDetailed,

		/**
		 * Title
		 * @var string
		 */
		$title;

	/**
	 * Construct a Exception object.
	 *
	 * @param \Exception Exception
	 * @param bool       Is it a detailed view of the description.
	 * @param string     Title
	 * @param string     Description
	 */
	public function __construct(
		\Exception   $exception,
		/* Bool   */ $isDetailed,
		/* String */ $title       = 'Exception',
		/* String */ $description = 'An unexpected exception was thrown.')
	{
		$this->description = $description;
		$this->exception   = $exception;
		$this->isDetailed  = $isDetailed;
		$this->title       = $title;	  
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view (of the data) to be written.
	 *
	 * @param mixed[] Parameters for retrieving the view.
	 *
	 * @return mixed[] The view data.
	 */	
	public function get(Array $params = array())
	{
		$exceptionElements = array(
			array('div', array('class' => 'Description'), $this->description));

		if ($this->isDetailed)
		{
			$exceptionElements[] =
				array('p',
				      array('class' => 'Message'),
				      $this->exception->getMessage());
			
			$exceptionElements[] = array(
				'pre',
				array('class' => 'Trace'),
				$this->exception->getTraceAsString());
		}
		
		return array(
			'div',
			array('class' => 'Message_Box System'),
			array(array('div',
			            array('class' => 'Title'),
			            $this->title),
			      array('div',
			            array('class' => 'Exception'),
			            $exceptionElements)));
	}
}
// EOF
