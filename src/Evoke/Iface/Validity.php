<?php
namespace Evoke\Iface;

interface Validity
{
   public function isValid($fieldset);
   public function getFailures();   
}
// EOF
