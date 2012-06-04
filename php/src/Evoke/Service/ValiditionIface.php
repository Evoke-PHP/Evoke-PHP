<?php
namespace Evoke\Service;

interface ValiditionIface
{
	public function isValid($fieldset);
	public function getFailures();   
}
// EOF
