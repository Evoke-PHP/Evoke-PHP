<?php
/**
 * XHTML Head View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

/**
 * XHTML Head View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2013 Paul Young
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
	protected $description, $css, $js,	$keywords, $title;

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
	                            Array        $css = array(),
	                            Array        $js  = array())
	{
		$this->description = $description;
		$this->keywords    = $keywords;
		$this->title       = $title;
		$this->css         = $css;
		$this->js          = $js;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add the CSS source file to the list of items to be written in the
	 * document head.
	 *
	 * @param string CSS source file.
	 */
	public function addCSS($source)
	{
		$this->css[] = (string)$source;
	}

	/**
	 * Add the JS source file to the list of items to be written in the
	 * document head.
	 *
	 * @param string JS source file.
	 */
	public function addJS($source)
	{
		$this->js[] = (string)$source;
	}

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

		foreach ($this->css as $cssSrc)
		{
			$headElements[] = array(
				'link',
				array('type' => 'text/css',
				      'href' => $cssSrc,
				      'rel'  => 'stylesheet'));
		}

		foreach ($this->js as $jsSrc)
		{
			$headElements[] = array(
				'script',
				array('type' => 'text/javascript',
				      'src'  => $js));
		}

		return array('head', array(), $headElements);
	}
	
	/**
	 * Set the description for the page.
	 *
	 * @param string Description for the page.
	 */
	public function setDescription($description)
	{
		$this->description = (string)$description;
	}

	/**
	 * Set the keywords for the page.
	 *
	 * @param string Keywords for the page.
	 */
	public function setKeywords($keywords)
	{
		$this->keywords = (string)$keywords;
	}
	
	/**
	 * Set the title for the page.
	 *
	 * @param string Title for the page.
	 */
	public function setTitle($title)
	{
		$this->title = (string)$title;
	}
}
// EOF