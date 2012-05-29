<?php
namespace Evoke\HTTP;

interface RequestIface
{
	public function parseAccept();
	
	public function parseAcceptLanguage();
}
// EOF