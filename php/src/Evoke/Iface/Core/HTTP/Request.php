<?php
namespace Evoke\Iface\Core\HTTP;

interface Request
{
	public function parseAccept();
	
	public function parseAcceptLanguage();
}
// EOF