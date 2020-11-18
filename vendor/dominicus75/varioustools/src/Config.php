<?php
/*
 * @file Config.php
 * @package VariousTools
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\VariousTools;

class Config
{

  private $config;

  public function __construct(string $className, string $configDirectory = null){

    if(is_null($configDirectory)) {
      $dir = dirname(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."config";
    } else {
      $dir = $configDirectory;
    }

    if(is_dir($dir)) {
      if(file_exists($dir.DIRECTORY_SEPARATOR.$className.".php")){
        $this->config = require_once $dir.DIRECTORY_SEPARATOR.$className.".php";
      } else {
        throw new \InvalidArgumentException($dir.DIRECTORY_SEPARATOR.$className.".php nem létezik");
      }
    } else {
      throw new \InvalidArgumentException("A megadott $dir nem létezik, vagy nem könyvtár");
    }

  }

  public function has(string $key):bool {
    return array_key_exists($key, $this->config);
  }

  public function get(string $key) {
    return $this->has($key) ? $this->config[$key] : null;
  }

  public function getKeys():array {
    return array_keys($this->config);
  }

  public function getConfig():array {
    return $this->config;
  }

}
