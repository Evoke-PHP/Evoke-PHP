<?php
namespace Evoke\Core\Iface;

interface Data extends \ArrayAccess, \Iterator
{
	public function setData(Array $data);
}
// EOF
