<?php
/**
 * Empty Record List View.
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\Model\Data\TranslationsIface;

/**
 * Empty Record List View.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class RecordListEmpty extends View
{
	/**
	 * Translations
	 * @var TranslationsIface
	 */
	protected $translations;

	/**
	 * Construct a RecordListEmpty object.
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

	public function get(Array $params = array())
	{

	}	
}
// EOF
