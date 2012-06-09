<?php
namespace Evoke\View\Text;

use Evoke\View\ViewIface;

interface TranslatorIface extends ViewIface
{
	public function getLanguage();
	
	public function getLanguages();

	public function tr($trKey);
}
// EOF