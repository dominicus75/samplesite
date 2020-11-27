<?php
/*
 * @file PDO.php
 * @package VariousTools
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\VariousTools;

use Dominicus75\VariousTools\Config as Config;

class PDO extends \PDO
{

  protected static $instance = null;
  protected static $options  = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ];

  private function __construct(Config $conf) {

      $config = $conf->getConfig();

      try {
        self::$instance = parent::__construct(
          $config['datasource'],
          $config['username'],
          $config['password'],
          self::setOptions($config['options'])
        );
        self::$instance->exec('set names utf8');
      } catch(\PDOException $pdoe) {
        echo $pdoe->getMessage();
      }

  }

  private static function setOptions($options):array {
    return is_null($options) ? self::$options : $options;
  }

  public static function getInstance(Config $config):self {

    if (is_null(self::$instance)) {
      self::$instance = new self($config);
    }
    return self::$instance;

  }

}
