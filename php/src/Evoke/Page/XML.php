<?php
namespace Evoke\Page;

use Evoke\Core\Iface;

abstract class XML extends Base
{
	/** @property $start
	 *  CSS and JS \array to be linked at the start of the page.
	 */
	protected $start;

	/** @property $startBase
	 *  CSS and JS \array that forms the base CSS and JS for all pages.
	 */
	protected $startBase;
	
	/** @property $Translator
	 *  Translator \object
	 */
	protected $Translator;

	/** @property $Writer
	 *  XHTML Writing Resource
	 */
	protected $Writer;
   
	public function __construct(Array $setup)
	{
		$setup += array('Start'      => array(),
		                'Start_Base' => array(
			                'CSS' => array('/csslib/global.css',
			                               '/csslib/common.css')),
		                'Translator' => NULL,
		                'Writer'     => NULL);

		if (!$setup['Translator'] instanceof Iface\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}
      
		if (!$setup['Writer'] instanceof Iface\Writer\Page)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Page Writer');
		}
      
		parent::__construct($setup);

		$this->start      = $setup['Start'];
		$this->startBase  = $setup['Start_Base'];
		$this->Translator = $setup['Translator'];
		$this->Writer     = $setup['Writer'];
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function load()
	{
		$this->start();
		$this->content();
		$this->end();
		$this->output();
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	protected function end()
	{
		$this->Writer->writeEnd();
	}

	protected function output()
	{
		$this->Writer->output();
	}
   
	protected function start()
	{
		$start = $this->startBase;

		foreach ($this->start as $key => $entry)
		{
			// Arrays should be appended to with only the new elements.
			if (isset($start[$key]) && is_array($start[$key]))
			{
				$start[$key] = array_merge($start[$key],
				                           array_diff($entry, $start[$key]));
			}
			else
			{
				$start[$key] = $entry;
			}
		}

		if (!isset($start['Title']))
		{
			$start['Title'] = $this->Translator->get('Title', $_SERVER['PHP_SELF']);
		}

		if (!isset($start['Keywords']))
		{
			$start['Keywords'] = $this->Translator->get('Keywords', $_SERVER['PHP_SELF']);
		}
      
		$this->Writer->writeStart($start);
	}
   
	/********************/
	/* Abstract Methods */
	/********************/

	abstract protected function content();
}
// EOF
