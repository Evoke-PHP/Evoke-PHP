<?php
use Evoke\Service\Autoload\PSR0Namespace;

$baseDir = dirname(dirname(dirname(__DIR__)));
$evokeDir = $baseDir . '/src/php/';

// Initialize the autoloader.
$autoloadDir = $evokeDir . 'Evoke/Service/Autoload/';
require $autoloadDir . 'AutoloadIface.php';
require $autoloadDir . 'PSR0Namespace.php';

spl_autoload_register([new PSR0Namespace($evokeDir, 'Evoke\\'), 'load']);
// EOF
