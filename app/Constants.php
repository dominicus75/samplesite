<?php

/*
 * @file Constants.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


define('DSR', DIRECTORY_SEPARATOR);
define('PRD', dirname(__DIR__).DSR);
define('APP', PRD.'app'.DSR);
define('VEN', PRD.'vendor'.DSR);
define('PUB', PRD.'public'.DSR);
define('THMS', APP.'view'.DSR.'themes'.DSR);
define('CTPL', THMS.'common'.DSR);
define('UTPL', THMS.'user'.DSR.'template'.DSR);
define('UCSS', THMS.'user'.DSR.'css'.DSR);
define('ATPL', THMS.'admin'.DSR.'template'.DSR);
define('ACSS', THMS.'admin'.DSR.'css'.DSR);
define('ETPL', THMS.'entrance'.DSR.'template'.DSR);
define('ECSS', THMS.'entrance'.DSR.'css'.DSR);
