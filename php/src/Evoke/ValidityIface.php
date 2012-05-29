<?php
namespace Evoke;

interface ValidityIface
{
	public function isValid($fieldset);
	public function getFailures();   
}
// EOF
