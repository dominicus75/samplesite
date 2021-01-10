<?php

/*
 * @file autoload.php
 * @package dominicus75
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


spl_autoload_register(function ($class_name) {

  $DSR = DIRECTORY_SEPARATOR;
  $PRD = dirname(__DIR__).$DSR;   // Projekt Root Directory
  $APP = $PRD."app".$DSR;         // APPlication root directory
  $VEN = $PRD."vendor".$DSR;      // VENdor root directory

  $fully_qualified_name = explode("\\", $class_name);

  $vendor = strtolower(array_shift($fully_qualified_name)).$DSR;
  $class  = array_pop($fully_qualified_name);
  $file   = (preg_match("/^Applic/i", $vendor)) ? $APP : $VEN.$vendor ;

  $numberOfItems = count($fully_qualified_name);

  if($numberOfItems == 0) {
    $file .= $class.".php";
  } elseif($numberOfItems == 1){
    $namespace = strtolower($fully_qualified_name[0]).$DSR;
    $file .= (preg_match("/^applic/i", $vendor))
    ? $namespace.$class.".php"
    : $namespace."src".$DSR.$class.".php";
  } else {
    $namespace = strtolower(array_shift($fully_qualified_name));
    $subNamespace = implode($DSR, $fully_qualified_name).$DSR;
    if($vendor == 'psr' && $namespace = 'http') {
      $file .= $namespace."-".$subNamespace."src".$DSR.$class.".php";
    } else {
      $file .= (preg_match("/^applic/i", $vendor))
      ? $namespace.$DSR.$subNamespace.$class.".php"
      : $namespace.$DSR."src".$DSR.$subNamespace.$class.".php";
    }
  }

  if (file_exists($file)) { require $file; }

});

