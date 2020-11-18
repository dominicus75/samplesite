<?php

/*
 * @file autoload.php
 * @package amdg
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

define("DSR", DIRECTORY_SEPARATOR);
define("PRD", dirname(__DIR__).DSR);  // Projekt Root Directory
define("APP", PRD."app".DSR);         // APPlication root directory
define("VEN", PRD."vendor".DSR);      // VENdor root directory

spl_autoload_register(function ($class_name) {

  $fully_qualified_name = explode("\\", $class_name);
  $vendor = strtolower(array_shift($fully_qualified_name)).DSR;

  $numberOfItems = count($fully_qualified_name);

  if($numberOfItems == 1) {
    $class = $fully_qualified_name[0];
    $file = (preg_match("/^Applic/i", $vendor))
    ? APP.$class.".php"
    : VEN.$vendor.$class.".php" ;
  } elseif($numberOfItems == 2){
    $namespace = strtolower($fully_qualified_name[0]).DSR;
    $class = $fully_qualified_name[1];
    $file = (preg_match("/^applic/i", $vendor))
    ? APP.$namespace.$class.".php"
    : VEN.$vendor.$namespace."src".DSR.$class.".php";
  } else {
    $namespace = strtolower(array_shift($fully_qualified_name)).DSR;
    $class = array_pop($fully_qualified_name);
    $subNamespace = DSR.implode(DSR, $fully_qualified_name).DSR;
    $file = (preg_match("/^applic/i", $vendor))
    ? APP.$namespace.$subNamespace.$class.".php"
    : VEN.$vendor.$namespace."src".$subNamespace.$class.".php";
  }

  if (file_exists($file)) { require $file; }

});

