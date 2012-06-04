<?php
namespace Evoke\View\Text;

use Evoke\View\ViewIface;

interface TranslatorIface extends ViewIface
{
	public function getLanguage();
	
	public function getLanguages();
	
	public function setLanguage($setLang='');

	public function tr($trKey, $page='default');
}
// EOF