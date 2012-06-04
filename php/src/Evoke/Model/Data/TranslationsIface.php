<?php
namespace Evoke\Model\Data;

interface TranslationsIface extends DataIface
{
	/** Get the current language that the translator is translating to.
	 *  @return @string The current language.
	 */
	public function getLanguage();

	/** Get all of the languages that the translations are provided in.
	 *  @return @array The languages that we have translations for.
	 */
	public function getLanguages();

	/** Clear the language so that it can be set from a fresh start.
	 */
	public function resetLanguage();
	
	public function setLanguage($lang = NULL);
}

// EOF
