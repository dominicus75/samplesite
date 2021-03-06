<?php
/*
 * @package Model
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Model;

use \Dominicus75\Config\Config;

class Table
{

  /**
   *
   * @var \Dominicus75\Model\PDO a singleton instance of PDO
   *
   */
  private PDO $database;

  /**
   *
   * @var string name of the current table (e. g. 'pages' or 'users')
   *
   */
  private string $table;

  /**
   *
   * @var array what contains the name of the primary key column (e. g. 'id')
   * and this column has auto_increment attribute or not in form
   * $primaryKey = ['name' => string, 'auto_increment' => bool]
   *
   */
  private array $primaryKey;

  /**
   *
   * @var array list of columns, what belong to the current table
   *
   */
  private array $columns;


  /**
   *
   * @param $confOrInstance an instance of \Dominicus75\Config\Config
   * or an instance of \Dominicus75\Model\PDO
   * @param string $table name of the current table
   * @throws \PDOException if
   * - $table is not found in this database
   * - current PDO driver is not supported (now only mysql supported yet)
   * - PDOStatement::fetchAll() or execute() returns with false
   *
   */
  public function __construct($confOrInstance, string $table){

    try {

      if($confOrInstance instanceof \Dominicus75\Config\Config) {
        $this->database = PDO::getInstance($confOrInstance);
      } elseif($confOrInstance instanceof \Dominicus75\Model\PDO) {
        $this->database = $confOrInstance;
      }

      if($this->database->hasTable($table)) {
        $this->table = $table;
      } else {
        throw new \PDOException($table. 'is not found in this database');
      }

      $this->columns    = $this->database->getColumns($this->table);
      $this->primaryKey = $this->database->getPrimaryKey($this->table);

    } catch(\PDOException $pdoe) { throw $pdoe; }

  }

  /**
   *
   * @param void
   * @return \Dominicus75\Model\PDO a singleton instance of PDO
   *
   */
  public function getDatabase(): PDO { return $this->database; }

  /**
   *
   * @param void
   * @return string name of the current table (e. g. 'pages' or 'users')
   *
   */
  public function getTable(): string { return $this->table; }

  /**
   *
   * @param string $column name of the requested column
   * @return bool Returns true if $column is found in $this->table, false otherwise
   *
   */
  public function hasColumn($column): bool {
    return array_key_exists($column, $this->columns);
  }

  /**
   *
   * @param string $column name of the requested column
   * @return array|null
   * array in (string)':column_name' => (int)column_type (\PDO::PARAM_STR or \PDO::PARAM_INT)
   * or null, if column does not exists
   *
   */
  public function getColumn($column): ?array {
    if($this->hasColumn($column)) {
      return $this->columns[$column];
    } else { return null; }
  }

  /**
   *
   * @param void
   * @return array
   * array in (string)'column_name' => [(string)':column_name' => (int)column_type]
   *
   */
  public function getColumns(): array { return $this->columns; }

  /**
   *
   * @param void
   * @return string name of the primary key column (e. g. 'id')
   *
   */
  public function getPrimaryKey(): string { return $this->primaryKey['name']; }

  /**
   * Check if a PRIMARY KEY is auto increment or not
   * @param void
   * @return bool
   *
   */
  public function isPrimaryAutoIncrement(): bool { return $this->primaryKey['auto_increment']; }

  /**
   * It is used to insert a new record in this table
   *
   * @param array $content in 'key' => 'value' form
   * (e. g. 'url' => 'aboutus', 'title' => 'About us')
   * @return bool Returns true on success or false on failure
   *
   */
  public function insert(array $content): bool {

    if(empty($content)) { return false; }

    $fields = '';
    $variables = '';

    foreach($this->columns as $name => $properties) {
      if($name == $this->primaryKey['name'] && $this->primaryKey['auto_increment']) {
        continue;
      } else {
        $fields .= '`'.$name.'`, ';
        $variables .= $properties[0].', ';
      }
    }
    $fields = rtrim($fields, ', ');
    $variables = rtrim($variables, ', ');

    $sql= "INSERT INTO `".$this->table."` ($fields) VALUES ($variables)";

    $statement = $this->database->prepare($sql);

    foreach($this->columns as $name => $properties) {
      if($name == $this->primaryKey['name'] && $this->primaryKey['auto_increment']) {
        continue;
      } else {
        $statement->bindParam($properties[0], $content[$name], $properties[1]);
      }
    }

    return $statement->execute();

  }

  /**
   *
   * @param string $statements
   * values are arrays what contain further statements to WHERE clause
   * (e. g. 0 => ['AND', 'type', '=', 'page'], 1 => ['AND NOT', 'type', '=', 'article'])
   *
   * @return array sanitized statements in an arranged array
   *
   * @throws InvalidFieldNameException if field name is not valid
   * @throws InvalidStatementException
   * if count of statement array items is not 4, or
   * if given logical operator is not allowed, or
   * if given columnname is invalid, or
   * if given comparison operator is not allowed
   *
   */
  private function sanitizeWhereStatements(array $statements): array {

    $result = [];
    $index = 0;

    foreach($statements as $statement) {


      if(count($statement) == 4) {
        if(preg_match("/^(AND|OR|AND NOT)$/i", $statement[0])) {
          $result[$index]['logical'] = $statement[0];
        } else {
          throw new InvalidStatementException($statement[0]." logical operator is not allowed");
        }
        if($this->hasColumn($statement[1])) {
          $result[$index]['column'] = $statement[1];
        } else {
          throw new InvalidFieldNameException("The {$statement[1]} columnname is not valid");
        }
        if(preg_match("/^(=|>|<|<>|<=|>=)$/i", $statement[2])) {
          $result[$index]['comparison'] = $statement[2];
        } else {
          throw new InvalidStatementException($statement[2]." comparison operator is not allowed");
        }
        if(preg_match("/^([\p{L}\p{N}\,\.\s\/@])/iu", $statement[3])) {
          $result[$index]['value'] = preg_replace("/(\s?.*\s?=\s?.*\s?|\s?drop\s?|;|\s?--\s?)/iu", '', $statement[3]);
        } else {
          throw new InvalidStatementException("Invalid characters!");
        }
        $index++;
      } else {
        throw new InvalidStatementException("The statement array must be contains 4 items");
      }

    }

    return $result;

  }


  /**
   * It is used to select data from this table
   *
   * @param array $key
   * $key[0] is key name (e. g. 'id') or null
   * $key[1] is value belongs to key (e. g. '1234' or 'contact') or null
   * @param array $fields list of selected columns, if it is empty, then select all columns (*)
   * @param array $furtherConditions
   * values are arrays what contain further conditions to WHERE clause
   * (e. g. 0 => ['AND', 'type', '=', 'page'], 1 => ['AND NOT', 'type', '=', 'article'])
   * if it is empty, there are not conditions
   * @param bool $fetchAll if it is true, the PDOStatement::fetchAll() will run,
   * if false PDOStatement::fetch()
   *
   * @return array the result of the SELECT statement or empty array
   * @throws InvalidFieldNameException if field not exists
   *
   */
  public function select(array $key, array $fields, array $furtherConditions, bool $fetchAll = false): array {

    if(is_null($key[0]) && is_null($key[1])) {
      $where      = '';
    } elseif($this->hasColumn($key[0])) {
      $fieldName  = $key[0];
      $fieldValue = $key[1];
      $where      = " WHERE ".$fieldName." = ".$this->columns[$fieldName][0];
    } else {
      throw new InvalidFieldNameException("The {$key[0]} not exists in the {$this->table} table");
    }

    if(!empty($fields)) {
      $columns = '';
      foreach($fields as $field) {
        if($this->hasColumn($field)) {
          $columns .= $field.', ';
        } else {
          throw new InvalidFieldNameException("The $field not exists in the {$this->table} table");
        }
      }
      $columns = rtrim($columns, ', ');
    } else { $columns = '*'; }

    $sql  = "SELECT ".$columns." FROM ".$this->table;
    $sql .= $where;

    if(!empty($furtherConditions)) {
      $conditions = $this->sanitizeWhereStatements($furtherConditions);
      if(empty($where)) { $conditions[0]['logical'] = " WHERE "; }
      foreach($conditions as $condition) {
        $sql .= " ".$condition['logical']." ".$condition['column'];
        $sql .= " ".$condition['comparison']." ".$this->columns[$condition['column']][0];
      }
    }

    $statement = $this->database->prepare($sql);

    if($this->hasColumn($key[0])) {
      $statement->bindParam($this->columns[$fieldName][0],
                            $fieldValue,
                            $this->columns[$fieldName][1]);
    }

    if(!empty($furtherConditions)) {
      foreach($this->sanitizeWhereStatements($furtherConditions) as $condition) {
        $statement->bindParam($this->columns[$condition['column']][0],
                              $condition['value'],
                              $this->columns[$condition['column']][1]);
      }
    }

    $statement->execute();
    $result = $fetchAll ? $statement->fetchAll() : $statement->fetch(PDO::FETCH_ASSOC);

    if($result) {
      return $result;
    } else { return []; }

  }

  /**
   * It is used to modify an existing record in this table
   *
   * @param array $content in 'key' => 'value' form
   * (e. g. 'url' => 'aboutus', 'title' => 'About us')
   * @param array $key
   * $key[0] is key name (e. g. 'id')
   * $key[1] is value belongs to key (e. g. '1234' or 'contact')
   * @return bool Returns true on success or false on failure
   *
   */
  public function update(array $content, array $key): bool {

    if(empty($content)) { return false; }

    if($this->hasColumn($key[0])) {
      $fieldName  = $key[0];
      $fieldValue = $key[1];
      $sql  = "UPDATE ".$this->table." SET ";
      foreach($content as $name => $value) {
        $sql .= $name." = ".$this->columns[$name][0].", ";
      }
      $sql = rtrim($sql, ', ');
      $sql .= " WHERE ".$fieldName." = ".$this->columns[$fieldName][0];
      $statement = $this->database->prepare($sql);
      $statement->bindParam($this->columns[$fieldName][0], $fieldValue, $this->columns[$fieldName][1]);

      foreach($content as $name => $value) {
        $statement->bindParam($this->columns[$name][0], $value, $this->columns[$name][1]);
      }

      return $statement->execute();
    } else {
      throw new InvalidFieldNameException("The {$key[0]} not exists in the {$this->table} table");
    }

  }

  /**
   * It is used to delete an existing record in this table
   *
   * @param array $key the deletable row's key (key name and value)
   * $key[0] is key name (e. g. 'id')
   * $key[1] is value belongs to key (e. g. '1234' or 'contact')
   * @return bool Returns true on success or false on failure
   *
   */
  public function delete(array $key): bool {

    if($this->hasColumn($key[0])) {
      $fieldName  = $key[0];
      $fieldValue = $key[1];
    } else {
      throw new InvalidFieldNameException("The {$key[0]} not exists in the {$this->table} table");
    }

    $sql  = "DELETE FROM ".$this->table;
    $sql .= " WHERE ".$fieldName." = ".$this->columns[$fieldName][0];
    $statement = $this->database->prepare($sql);
    $statement->bindParam($this->columns[$fieldName][0],
                          $fieldValue,
                          $this->columns[$fieldName][1]);

    return $statement->execute();

  }


}
