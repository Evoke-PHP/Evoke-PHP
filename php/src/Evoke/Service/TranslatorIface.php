<?php
namespace Evoke\Service;

interface TranslatorIface
{
	public function getLanguage();
	
	public function getLanguages();
	
	public function setLanguage($setLang='');

	public function get($trKey, $page='default');
}
// EOF