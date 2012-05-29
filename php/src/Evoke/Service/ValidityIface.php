<?php
namespace Evoke\Service;

interface ValidityIface
{
	public function isValid($fieldset);
	public function getFailures();   
}
// EOF
