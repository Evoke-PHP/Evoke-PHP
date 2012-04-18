<?php
namespace Evoke\Core\Iface\HTTP;

interface Request
{
	public function parseAccept();
	
	public function parseAcceptLanguage();
}
// EOF