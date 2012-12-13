<?php
/**
 * Heading View.
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\Model\Data\TranslationsIface;

/**
 * Heading View.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Heading implements ViewIface
{
	/**
	 * Translations
	 * @var TranslationsIface
	 */
	protected $translations;

	/**
	 * Construct a Heading view.
	 *
	 * @param TranslationsIface Translations.
	 */
	public function __construct(TranslationsIface $translations)
	{
		$this->translations = $translations;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the heading for the specified field.
	 *
	 * @param string[] Parameters for the view.
	 * @return mixed[] The view of the heading.
	 */
	public function get(Array $params = array())
	{
		$params += array('Field' => 'UNKNOWN');
		
		return array('div',
		             array('class' => 'Heading ' . $params['Field']),
		             $this->translations->tr($params['Field']));
	}
}
// EOF