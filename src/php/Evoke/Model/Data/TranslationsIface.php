<?php
/**
 * Translations Data Interface
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

/**
 * Translations Data Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Model
 */
interface TranslationsIface extends DataIface
{
	/**
	 * Get the current language that the translator is translating to.
	 *
	 * @return string The current language.
	 */
	public function getLanguage();

	/**
	 * Get all of the languages that the translations are provided in.
	 *
	 * @return mixed[] The languages that we have translations for.
	 */
	public function getLanguages();

	/**
	 * Get the key that is used for the language query parameter.
	 *
	 * @return string The key used in query parameters to set the language.
	 */
	public function getLanguageKey();

	/**
	 * Clear the language so that it can be set from a fresh start.
	 */
	public function resetLanguage();

	/**
	 * Set the language.
	 */
	public function setLanguage($lang = NULL);

	/**
	 * Get the translation data for a single entry.
	 *
	 * @param string The key for the translation to retrieve.
	 *
	 * @return string The translated string.
	 */
	public function tr(/* String */ $key);
	
	/**
	 * Get the translation data for a single entry in the specified language.
	 *
	 * @param string The key for the translation to retrieve.
	 * @param string The language that we want the translation in.
	 *
	 * @return string The translated string.
	 */
	public function trSpecific($key, $language);	
}
// EOF
