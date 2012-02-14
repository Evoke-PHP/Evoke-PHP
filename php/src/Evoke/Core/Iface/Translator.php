<?php
namespace Evoke\Core\Iface;

interface Translator
{
	public function getLanguage();
	
	public function getLanguages();
	
	public function setLanguage($setLang='');

	public function get($trKey, $page='default');
}
// EOF