<?php
/*
 * @file AbstractModel.php
 * @package Core
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Core\Model;

abstract class AbstractModel
{

  protected PDO $database;
  protected string $table;
  protected string $primaryKey;
  protected array $columns = [];
  protected array $content = [];
  protected array $updated = [];

  protected function __construct(
    $confOrInstance,
    string $table
  ){

    try {

      if($confOrInstance instanceof \ArrayAccess) {
        $this->database = PDO::getInstance($confOrInstance);
      } elseif($confOrInstance instanceof PDO) {
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


  protected function hasColumn($field): bool {
    return array_key_exists($field, $this->columns);
  }


  protected function setContent(array $content): void {

    foreach($content as $fieldName => $value) {
      try {
        $this->setField($fieldName, $value);
      } catch(InvalidFieldNameException $e) { throw $e; }
    }

  }


  protected function updateContent(array $content = []): void {

    if(!empty($content)) {
      foreach($content as $field => $value) {
        try {
          $this->updateField($field, $value);
        } catch (InvalidFieldNameException $e) { throw $e; }
      }
    }

  }


  protected function setField(string $field, $value): void {

    if($this->hasColumn($field)) {

      if(!isset($this->content[$field])) {
        $this->content[$field] = $value;
      } else { throw new InvalidFieldNameException("The $field is already set"); }

    } else { throw new InvalidFieldNameException("The $field field name is not valid"); }

  }


  protected function updateField(string $field, $value): void {

    if($this->hasField($field)) {

      if($field === $this->primaryKey) {
        throw new InvalidFieldNameException("The primary key is immutable!");;
      }

      $this->updated[$field] = $value;

    } else { throw new InvalidFieldNameException("The $field field is not exists"); }

  }


  protected function insert(): bool {

    if(empty($this->content)) { return false; }

    $fields = '';
    $variables = '';

    foreach($this->columns as $name => $properties) {
      if($name == $this->primaryKey && is_null($this->content[$this->primaryKey])) {
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
      if($name == $this->primaryKey && is_null($this->content[$this->primaryKey])) {
        continue;
      } else {
        $statement->bindParam($properties[0], $this->content[$name], $properties[1]);
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
        $result[$index]['value'] = $statement[3];
        $index++;
      } else {
        throw new InvalidStatementException("The statement array must be contains 4 items");
      }

    }

    return $result;

  }


  /**
   *
   * @param array $key
   * $key[0] is key name (e. g. 'id')
   * $key[1] is value belongs to key (e. g. '1234' or 'contact')
   * @param array $furtherConditions
   * values are arrays what contain further conditions to WHERE clause
   * (e. g. 0 => ['AND', 'type', '=', 'page'], 1 => ['AND NOT', 'type', '=', 'article'])
   *
   * @return array the result of the SELECT statement or empty array
   * @throws InvalidFieldNameException if field not exists
   *
   */
  protected function select(array $key, array $fields = [], array $furtherConditions = []): array {

    if($this->hasColumn($key[0])) {
      $fieldName  = $key[0];
      $fieldValue = $key[1];
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
    $sql .= " WHERE ".$fieldName." = ".$this->columns[$fieldName][0];

    if(!empty($furtherConditions)) {
      foreach($this->sanitizeWhereStatements($furtherConditions) as $condition) {
        $sql .= " ".$condition['logical']." ".$condition['column'];
        $sql .= " ".$condition['comparison']." ".$this->columns[$condition['column']][0];
      }
    }

    $statement = $this->database->prepare($sql);
    $statement->bindParam($this->columns[$fieldName][0],
                          $fieldValue,
                          $this->columns[$fieldName][1]);

    if(!empty($furtherConditions)) {
      foreach($this->sanitizeWhereStatements($furtherConditions) as $condition) {
        $statement->bindParam($this->columns[$condition['column']][0],
                              $condition['value'],
                              $this->columns[$condition['column']][1]);
      }
    }

    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if($result) {
      return $result;
    } else { return []; }

  }

  protected function update(string $key = ''): bool {

    if(empty($this->updated)) { return false; }

    if(empty($key)){
      $fieldName = $this->primaryKey;
    } elseif($this->hasColumn($key)) {
      $fieldName = $key;
    } else { return false; }

    $sql  = "UPDATE ".$this->table." SET ";

    foreach($this->updated as $name => $value) {
      $sql .= $name." = ".$this->columns[$name][0].", ";
    }

    $sql = rtrim($sql, ', ');
    $sql .= " WHERE ".$fieldName." = ".$this->columns[$fieldName][0];

    $statement = $this->database->prepare($sql);

    foreach($this->updated as $name => $value) {
      $statement->bindParam($this->columns[$name][0], $value, $this->columns[$name][1]);
    }

    return $statement->execute();

  }

  protected function erase(string $key = ''): bool {

    if(empty($key)){
      $fieldName = $this->primaryKey;
    } elseif($this->hasColumn($key)) {
      $fieldName = $key;
    } else { return false; }

    $sql  = "DELETE FROM ".$this->table;
    $sql .= " WHERE ".$fieldName." = ".$this->columns[$fieldName][0];
    $statement = $this->database->prepare($sql);
    $statement->bindParam($this->columns[$fieldName][0],
                          $this->content[$fieldName],
                          $this->columns[$fieldName][1]);

    return $statement->execute();

  }


  public function getTableName(): string { return $this->table; }


  public function getContent(): array {

    if(!empty($this->content)) {
      return $this->content;
    } else {
      throw new ContentNotFoundException();
    }

  }

  abstract public function create(): bool;
  abstract public function read(array $url): array;
  abstract public function edit(array $url): bool;
  abstract public function delete(array $url): bool;

}
