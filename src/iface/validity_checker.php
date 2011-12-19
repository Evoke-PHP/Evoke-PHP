<?php


/// Interface Validity_Checker
interface Iface_Validity_Checker
{
   public function isValid($fieldset);
   public function getFailures();   
}

// EOF