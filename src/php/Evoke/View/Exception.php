<?php
/**
 * Exception View
 *
 * @package View
 */
namespace Evoke\View;

use InvalidArgumentException;

/**
 * Exception View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Exception extends View
{
	protected 
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
	 * @param bool       Is it a detailed view of the description.
	 * @param string     Title
	 * @param string     Description
	 */
	public function __construct(
		/* Bool   */ $isDetailed,
		/* String */ $title       = 'Exception',
		/* String */ $description = 'An unexpected exception was thrown.')
	{
		$this->description = $description;
		$this->isDetailed  = $isDetailed;
		$this->title       = $title;	  
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view (of the data) to be written.
	 *
	 * @return mixed[] The view data.
	 */	
	public function get()
	{
		$exception = reset($data->getRecord());
		
		// We specify exactly \Exception to avoid a clash with the classname.
		if (!$exception instanceof \Exception)
		{
			throw new InvalidArgumentException('needs data as Exception');
		}				
		
		$exceptionElements = array(
			array('div', array('class' => 'Description'), $this->description));

		if ($this->isDetailed)
		{
			$exceptionElements[] =
				array('p',
				      array('class' => 'Message'),
				      $exception->getMessage());
			
			$exceptionElements[] = array(
				'pre',
				array('class' => 'Trace'),
				$exception->getTraceAsString());
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
