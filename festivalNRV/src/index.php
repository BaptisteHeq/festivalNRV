<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\nrv\dispatch\Dispatcher;

session_start();

$action = $_GET['action'] ?? 'default';
$dispatcher = new Dispatcher($action);
$dispatcher->run();