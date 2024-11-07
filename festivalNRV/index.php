<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\nrv\dispatch\Dispatcher;
use iutnc\nrv\repository\NrvRepository;

NrvRepository::setConfig( 'db.config.ini' );

session_start();




$action = $_GET['action'] ?? 'default';
$dispatcher = new Dispatcher($action);
$dispatcher->run();