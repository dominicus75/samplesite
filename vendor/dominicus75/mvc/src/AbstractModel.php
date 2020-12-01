<?php
/*
 * @file AbstractModel.php
 * @package MVC
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\MVC;

abstract class AbstractModel
{

  protected PDO $pdo;
  protected string $table;
  protected string $primaryKey;
  protected $contentID;
  protected array $content = [];
  protected array $columns = [];
  protected array $updated = [];


  public function __construct(
    \ArrayAccess $pdoConfig,
    $contentID = ''
  ){

    try {

      $this->pdo = PDO::getInstance($pdoConfig);
      $this->contentID = $contentID;

    } catch(\PDOException $pdoe) { throw $pdoe; }

  }

  public function getTableName(): string { return $this->table; }

  public function hasPrimaryKey(): bool {
    return array_key_exists($this->primaryKey, $this->content);
  }

  public function getPrimaryKey() {
    return (array_key_exists($this->primaryKey, $this->content))
            ? $this->content[$this->primaryKey]
            : null ;
  }


  public function setContent(array $content = []): void {

    if(!empty($content)) {
      foreach($content as $fieldName => $value) {
        try {
          $this->setField($fieldName, $value);
        } catch(InvalidFieldNameException $e) { throw $e; }
      }
    } else if(!empty($this->contentID)) {
      $result = $this->select();
      if(!is_null($result)) {
        try {
         $this->setContent($result);
        } catch(InvalidFieldNameException $e) { throw $e; }
      } else { $this->content = []; }
    }

  }


  public function updateContent(array $content = []): void {

    if(!empty($content)) {

      foreach($content as $field => $value) {

        try {
          $this->updateField($field, $value);
        } catch (InvalidFieldNameException $e) { throw $e; }

      }

    }

  }

  public function getContent(): array { return $this->content; }


  public function setField($field, $value): void {

    if(array_key_exists($field, $this->columns)) {

      if(!isset($this->content[$field])) {
        $this->content[$field] = $value;
      } else { throw new InvalidFieldNameException("The $field is already set"); }

    } else { throw new InvalidFieldNameException("The $field field name is not valid"); }

  }


  public function updateField($field, $value): void {

    if(array_key_exists($field, $this->content)) {

      if($field === $this->primaryKey) {
        throw new InvalidFieldNameException("The primary key is immutable!");;
      }

      $this->updated[$field] = $value;

    } else { throw new InvalidFieldNameException("The $field field is not exists"); }

  }


  public function insert(): bool {

    if(empty($this->content)) { return false; }

    $fields = '';
    $variables = '';

    foreach($this->columns as $name => $properties) {
      //If there is no contentID, in that case AUTO INCREMENT
      if(empty($this->contentID) && $name == $this->primaryKey) { continue; }
      $fields .= $name.', ';
      $variables .= $properties[0].', ';
    }
    $fields = rtrim($fields, ', ');
    $variables = rtrim($variables, ', ');

    $sql= "INSERT INTO ".$this->table." ($fields) VALUES ($variables)";

    $statement = $this->pdo->prepare($sql);

    foreach($this->columns as $name => $properties) {
      if(empty($this->contentID) && $name == $this->primaryKey) { continue; }
      $statement->bindParam($properties[0], $this->content[$name], $properties[1]);
    }

    return $statement->execute();

  }


  public function select(array $params = []): ?array {

    if(empty($params)){
      $sql  = "SELECT * FROM `".$this->table;
      $sql .= "` WHERE ".$this->primaryKey."=".$this->columns[$this->primaryKey][0];
      $statement = $this->pdo->prepare($sql);
      $statement->bindParam($this->columns[$this->primaryKey][0],
                            $this->contentID,
                            $this->columns[$this->primaryKey][1]);
      $statement->execute();
      $result = $statement->fetch(PDO::FETCH_ASSOC);
      if($result) {
        return $result;
      } else { return null; }
    }

  }

  public function update(): bool {

    if(empty($this->updated)) { return false; }

    $sql  = "UPDATE `".$this->table."` SET ";

    foreach($this->updated as $name => $value) {
      $sql .= "`".$name."` = ".$this->columns[$name][0].", ";
    }

    $sql = rtrim($sql, ', ');
    $sql .= " WHERE ".$this->primaryKey." = ".$this->columns[$this->primaryKey][0];

    $statement = $this->pdo->prepare($sql);

    foreach($this->updated as $name => $value) {
      $statement->bindParam($this->columns[$name][0], $value, $this->columns[$name][1]);
    }

    $statement->bindParam($this->columns[$this->primaryKey][0],
                          $this->content[$this->primaryKey],
                          $this->columns[$this->primaryKey][1]);

    return $statement->execute();

  }

  public function delete(): bool {

    if(empty($this->contentID)) { return false; }

    $sql  = "DELETE FROM `".$this->table;
    $sql .= "` WHERE ".$this->primaryKey."=".$this->columns[$this->primaryKey][0];
    $statement = $this->pdo->prepare($sql);
    $statement->bindParam($this->columns[$this->primaryKey][0],
                          $this->content[$this->primaryKey],
                          $this->columns[$this->primaryKey][1]);

    return $statement->execute();

  }


}
