<?php
/*
 * @file PDO.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;


class PDO extends \PDO
{

  protected static $instance = null;
  protected static $options  = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ];

  private function __construct(\ArrayAccess $config) {

      try {

        self::$instance = parent::__construct(
          $config->offsetGet('datasource'),
          $config->offsetGet('username'),
          $config->offsetGet('password'),
          self::setOptions($config->offsetGet('options'))
        );

      } catch(\PDOException $e) { throw $e; }

  }

  private static function setOptions(?array $options): ?array {
    return is_null($options) ? self::$options : null;
  }

  public static function getInstance(\ArrayAccess $config):self {

    if (is_null(self::$instance)) {

      try {
        self::$instance = new self($config);
        self::$instance->exec('set names utf8');
        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(\PDOException $e) { throw $e; }

    }

    return self::$instance;

  }

}
