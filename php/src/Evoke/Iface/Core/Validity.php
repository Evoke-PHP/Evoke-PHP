<?php
namespace Evoke\Iface\Core;

interface Validity
{
	public function isValid($fieldset);
	public function getFailures();   
}
// EOF
