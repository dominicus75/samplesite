<?php

/*
 * @file Constants.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


define('THM', 'default');
define('DSR', DIRECTORY_SEPARATOR);
define('PRD', dirname(__DIR__).DSR);
define('APP', PRD.'app'.DSR);
define('VEN', PRD.'vendor'.DSR);
define('TPL', APP.'view'.DSR.'themes'.DSR.THM.DSR.'template'.DSR);
define('CSS', APP.'view'.DSR.'themes'.DSR.THM.DSR.'css'.DSR);
define('ATPL', APP.'view'.DSR.'themes'.DSR.'admin'.DSR.'template'.DSR);
define('ACSS', APP.'view'.DSR.'themes'.DSR.'admin'.DSR.'css'.DSR);
