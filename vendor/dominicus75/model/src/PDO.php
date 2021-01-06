<?php
/*
 * @file PDO.php
 * @package Model
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Model;

use \Dominicus75\Config\Config;


class PDO extends \PDO
{

  /**
   *
   * @var array default pdo options
   *
   */
  protected static $options  = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ];

  /**
   *
   * @var array list of pdo drivers what compatibile this implementation
   *
   */
  const DRIVERS = ['mysql'];

  /**
   *
   * @var \Dominicus75\Model\PDO current pdo instance
   *
   */
  protected static ?self $instance = null;

  /**
   *
   * @var string name of current pdo driver
   *
   */
  private string $driver;

  /**
   *
   * @var array List of tables what belongs to this database
   *
   */
  private array $tables;


  /**
   *
   * @param \Dominicus75\Config\Config config object, what implements
   * \ArrayAccess interface
   * @see https://www.php.net/manual/en/class.arrayaccess.php
   *
   * @throws \PDOException
   *
   */
  private function __construct(Config $config) {

    try {

      self::$instance = parent::__construct(
        $config->offsetGet('datasource'),
        $config->offsetGet('username'),
        $config->offsetGet('password'),
        self::setOptions($config->offsetGet('options'))
      );

    } catch(\PDOException $e) { throw $e; }

  }

  /**
   * Set options to PDO (parent) class constructor
   *
   * @param array|null $options
   * @return array|null
   *
   */
  private static function setOptions(?array $options): ?array {
    return is_null($options) ? self::$options : null;
  }

  /**
   *
   * @param \Dominicus75\Config\Config object, what implements
   * \ArrayAccess interface
   * @see https://www.php.net/manual/en/class.arrayaccess.php
   * @return a singleton instance of this class
   *
   * @throws \PDOException
   *
   */
  public static function getInstance(Config $config):self {

    if (is_null(self::$instance)) {

      try {
        self::$instance = new self($config);
        self::$instance->exec('set names utf8');
        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$instance->setDriver();
        self::$instance->setTables();
      } catch(\PDOException $e) { throw $e; }

    }

    return self::$instance;

  }

  /**
   * Setting of current pdo driver's name
   *
   * @param void
   * @return self
   *
   */
  private function setDriver(): self {

    $driver = parent::getAttribute(PDO::ATTR_DRIVER_NAME);

    if(in_array($driver, self::DRIVERS)) {
      $this->driver = $driver;
      return $this;
    } else {
      throw new \PDOException($driver. 'is not supported');
    }

  }

  /**
   * Querying of tables list from database
   *
   * @param void
   * @return self
   * @throws \PDOException if PDOStatement::fetchAll() or execute() returns with false
   *
   */
  private function setTables(): self {

    switch($this->driver) {
      case 'mysql':
        $sql = "SHOW TABLES";
      break;
    }

    $statement = self::$instance->query($sql);

    if($statement->execute()) {
      if($tables = $statement->fetchAll(PDO::FETCH_COLUMN)) {
        $this->tables = $tables;
        return $this;
      } else {
        throw new \PDOException('PDOStatement::fetchAll() function returned with false');
      }
    } else {
      throw new \PDOException('PDOStatement::execute() function returned with false');
    }

  }


  /**
   *
   * @param string $table Name of the searched table
   * @return bool Returns true if table is found in this database, false otherwise
   *
   */
  public function hasTable(string $table): bool { return in_array($table, $this->tables); }


  /**
   *
   * @param void
   * @return array List of tables what belongs to this database
   *
   */
  public function getTables(): array { return $this->tables; }


  /**
   *
   * @param string $table Name of the searched table
   * @return array List of columns what belongs to given table
   * @throws \PDOException
   * if given table is not found in this database
   * if PDOStatement::fetchAll() or execute() returns with false
   *
   */
  public function getColumns(string $table): array {

    if($this->hasTable($table)) {

      switch($this->driver) {
        case 'mysql':
          $sql = "SHOW COLUMNS FROM `$table`";
        break;
      }

      $statement = self::$instance->query($sql);

      if($statement->execute()) {
        if($columns = $statement->fetchAll()) {
          $result = [];
          foreach($columns as $column) {
            $type = (preg_match("/(char|text)/is", $column['Type'])) ? \PDO::PARAM_STR : \PDO::PARAM_INT;
            $result[$column['Field']] = [":".$column['Field'], $type];
          }
          return $result;
        } else {
          throw new \PDOException('PDOStatement::fetchAll() function returned with false');
        }
      } else {
        throw new \PDOException('PDOStatement::execute() function returned with false');
      }

    } else {
      throw new \PDOException($table. 'is not found in this database');
    }

  }


  /**
   *
   * @param string $table Name of the searched table
   * @return string Name of the PRIMARY KEY (e. g. 'id' or 'cid')
   * @throws \PDOException
   * if given table is not found in this database
   * if PDOStatement::fetchAll() or execute() returns with false
   *
   */
  public function getPrimaryKey(string $table): string {

    if($this->hasTable($table)) {

      switch($this->driver) {
        case 'mysql':
          $sql = "SHOW COLUMNS FROM `$table`";
        break;
      }

      $statement = self::$instance->query($sql);

      if($statement->execute()) {
        if($columns = $statement->fetchAll()) {
          $index = 0;
          while($index < count($columns)) {
            if($columns[$index]['Key'] == 'PRI') { return $columns[$index]['Field']; }
            $index++;
          }
        } else {
          throw new \PDOException('PDOStatement::fetchAll() function returned with false');
        }
      } else {
        throw new \PDOException('PDOStatement::execute() function returned with false');
      }

    } else {
      throw new \PDOException($table. 'is not found in this database');
    }

  }


}
