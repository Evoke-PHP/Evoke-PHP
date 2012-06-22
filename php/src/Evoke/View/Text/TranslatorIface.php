<?php
namespace Evoke\View\Text;

use Evoke\View\ViewIface;

/**
 * TranslatorIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
interface TranslatorIface extends ViewIface
{
	/**
	 * Get the current language that the translator is translating to.
	 *
	 * @return string The current language.
	 */
	public function getLanguage();

	/**
	 * Get all of the languages that the translator has translations for.
	 *
	 * @return string[] The list of languages.
	 */
	public function getLanguages();

	/**
	 * Translate the key to the current language.
	 *
	 * @return string The translation.
	 */
	public function tr($trKey);
}
// EOF