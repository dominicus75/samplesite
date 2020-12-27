<?php
/*
 * @file Config.php
 * @package Core
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Core;

class Config implements \ArrayAccess
{

  private $container;

  public function __construct(string $configFile, string $configDirectory = null){

    if(is_null($configDirectory)) {
      $dir = dirname(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."config";
    } else {
      $dir = $configDirectory;
    }

    if(is_dir($dir)) {
      if(file_exists($dir.DIRECTORY_SEPARATOR.$configFile.".php")){
        $this->container = require_once $dir.DIRECTORY_SEPARATOR.$configFile.".php";
      } else {
        throw new Exceptions\FileNotFoundException("Filename: ".$dir.DIRECTORY_SEPARATOR.$configFile.".php");
      }
    } else {
      throw new Exceptions\DirectoryNotFoundException("Directory name: ".$dir);
    }

  }


  public function offsetExists($offset): bool {
      return array_key_exists($offset, $this->container);
  }


  public function offsetGet($offset) {
    return ($this->offsetExists($offset)) ? $this->container[$offset] : null;
  }


  public function offsetSet($offset, $value): void {
    if (is_null($offset)) {
      $this->container[] = $value;
    } else {
      $this->container[$offset] = $value;
    }
  }


  public function offsetUnset($offset): void {
    unset($this->container[$offset]);
  }

}
