<?php
namespace Evoke\Iface\HTTP;

interface Request
{
	public function parseAccept();
	
	public function parseAcceptLanguage();
}
// EOF