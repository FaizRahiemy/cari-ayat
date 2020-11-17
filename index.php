<?php

require_once __DIR__ . "/AutoLoader.php";

use lib\MVC\router;
use lib\MVC\Controller;

$kernel = new router($_GET);
$controller = $kernel->getController();
$controller->ExecuteAction();
