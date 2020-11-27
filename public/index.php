<?php declare(strict_types=1);

session_start();
setlocale(LC_ALL, 'hu_HU.UTF-8');

require_once(dirname(__DIR__).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Constants.php');
require_once(dirname(__DIR__).DSR.'vendor'.DSR.'autoload.php');

Application\FrontController::run();

