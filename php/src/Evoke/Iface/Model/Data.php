<?php
namespace Evoke\Iface;

interface Data extends \ArrayAccess, \Iterator
{
	public function setData(Array $data);
}
// EOF
