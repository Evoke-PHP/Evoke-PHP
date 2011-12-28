<?php

$c = new Container();
$settings = $c->getShared('Settings');

// Define Error Constants.
$settings->set(
   'ERRORS' => array('NO_ERROR'             => 'No Error',
		     'UNKNOWN_TYPE_ERROR'   => 'Unknown Type Error',
		     'FORMAT_ERROR'         => 'Format Error',
		     'RANGE_ERROR'          => 'Range Error',
		     'REQUIRED_FIELD_ERROR' => 'Required Field Error',
		     'OVERFLOW_ERROR'       => 'Overflow Error'));



// EOF