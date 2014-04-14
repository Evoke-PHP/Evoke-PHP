<?php
/**
 * XHTML Head View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

use Evoke\View\ViewIface;

/**
 * XHTML Head View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class Head implements ViewIface
{
	/**
	 * Protected Properties
	 *
	 * @var string   $description Description of the page.
	 * @var string[] $css         CSS source files.
	 * @var string[] $js          JavaScript source files.
	 * @var string   $keywords    Keywords of the page.
	 * @var string   $title       Title of the page.
	 */
	protected $description, $cssSources, $jsSources, $keywords, $title;

	/**
	 * Construct a Head object.
	 *
	 * @param string   Description.
	 * @param string   Keywords.
	 * @param string   Title.
	 * @param string[] CSS source files.
	 * @param string[] JavaScript source files.
	 */
	public function __construct(/* string */ $description,
	                            /* string */ $keywords,
	                            /* string */ $title,
	                            Array        $cssSources = array(),
	                            Array        $jsSources  = array())
	{
		$this->description = $description;
		$this->keywords    = $keywords;
		$this->title       = $title;
		$this->cssSources  = $cssSources;
		$this->jsSources   = $jsSources;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the output from the view.
	 *
	 * @return mixed[] The output from the view.
	 */
	public function get()
	{
		$headElements = array(
			array('title', array(), $this->title),
			array('meta', array('content' => $this->title,
			                    'name'    => 'title')),
			array('meta', array('content' => $this->description,
			                    'name'    => 'description')),
			array('meta', array('content' => $this->keywords,
			                    'name'    => 'keywords')));

		foreach ($this->cssSources as $cssSrc)
		{
			$headElements[] = array(
				'link',
				array('type' => 'text/css',
				      'href' => $cssSrc,
				      'rel'  => 'stylesheet'));
		}

		foreach ($this->jsSources as $jsSrc)
		{
			$headElements[] = array(
				'script',
				array('type' => 'text/javascript',
				      'src'  => $jsSrc));
		}

		return array('head', array(), $headElements);
	}
}
// EOF