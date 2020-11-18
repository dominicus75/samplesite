<?php

/*
 * @file Constants.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


define("DSR", DIRECTORY_SEPARATOR);
define("PRD", dirname(__DIR__).DSR);  // Projekt Root Directory
define("APP", PRD."app".DSR);         // APPlication root directory
define("VEN", PRD."vendor".DSR);      // VENdor root directory
