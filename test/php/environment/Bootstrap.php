<?php
use Evoke\Service\Autoload\PSR0Namespace;

$baseDir = dirname(dirname(dirname(__DIR__)));
$evokeDir = $baseDir . '/src/php/';

// Initialize the autoloader.
$autoloadDir = $evokeDir . 'Evoke/Service/Autoload/';
require $autoloadDir . 'AutoloadIface.php';
require $autoloadDir . 'Autoload.php';
require $autoloadDir . 'PSR0Namespace.php';

$evokeAutoloader = new PSR0Namespace($evokeDir, 'Evoke\\');
$evokeAutoloader->register();
// EOF
