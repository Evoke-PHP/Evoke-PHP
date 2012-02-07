<?php
namespace Evoke\Core\Iface;

interface Validity
{
	public function isValid($fieldset);
	public function getFailures();   
}
// EOF
