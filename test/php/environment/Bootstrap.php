<?php
$baseDir = dirname(dirname(dirname(__DIR__)));
$evokeDir = $baseDir . '/src/php/';

// Initialize the autoloader.
require $evokeDir . 'Evoke/Service/Handler/HandlerIface.php';
require $evokeDir . 'Evoke/Service/Handler/Autoload.php';

$evokeAutoload = new \Evoke\Service\Handler\Autoload($evokeDir, 'Evoke\\');
$evokeAutoload->register();
// EOF
