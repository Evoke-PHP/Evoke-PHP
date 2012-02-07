<?php
namespace Evoke\Core\Iface\URI;

interface Mapper
{
	public function isAuthoritative();
	public function matches($uri);
	public function getParams($uri);
	public function getResponse($uri);
}
// EOF