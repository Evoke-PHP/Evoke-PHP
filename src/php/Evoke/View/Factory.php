<?php
/**
 * View Factory
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\Model\Data\TranslationsIface;

/**
 * View Factory
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Factory
{
	/**
	 * Translations
	 * @var TranslationsIface
	 */
	protected $translations;

	/**
	 * Construct a Factory object.
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
	 * Make a fixed view.
	 */
	public function makeFixed($fixed)
	{
		return new Fixed($fixed);
	}		
}
// EOF