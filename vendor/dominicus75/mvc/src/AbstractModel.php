<?php
/*
 * @file AbstractModel.php
 * @package MVC
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\MVC;

use \Dominicus75\VariousTools\Config;

abstract class AbstractModel implements ModelInterface
{

  protected PDO $pdo;
  protected string $table;
  protected ?string $contentId;
  protected ?array $content;
  protected ?array $columns;


  public function __construct(
    \ArrayAccess $pdoConfig,
    string $tableName,
    $contentId = null,
    ?array $content = null
  ){

    try {
      $this->pdo       = PDO::getInstance($pdoConfig);
      $this->table     = $tableName;
      $this->contentId = $contentId;
      $this->setContent();
    } catch(\PDOException $pdoe) { throw $pdoe; }

  }

  public function getTableName(): string { return $this->table; }

  public function getContentId() {
    return $this->contentId;
  }

  protected function setColumnTypes(?array $content = null): ?array {

    if(is_null($content)) { return null; }

    $result = [];

    foreach($content as $key => $value){

      $columnName = ":".$key;

      switch(gettype($value)){
        case: 'boolean': $columnType = \PDO::PARAM_BOOL;
        break;
        case: 'integer': $columnType = \PDO::PARAM_INT;
        break;
        case: 'string': $columnType = \PDO::PARAM_STR;
        break;
        case: 'double': $columnType = \PDO::PARAM_INT;
        break;
        default: $columnType = \PDO::PARAM_NULL;
        break;
      }

      $result[$columnName] = (int)$columnType;

    }

    return $result;

  }


  public function setContent(?array $content = null): void {

     if(is_null($this->content) && !is_null($content)) {
        $this->content = $content;
        $this->columns = $this->setColumnTypes($content);
     } else { $this->content = null; }

  }

  public function getContent(): ?array { return $this->content; }

  public function insert(?string $idName = null): bool {

    if(is_null($this->content)) { return false; }

    $table = $this->table;
    $fields = (is_null($idName)) ? '' : $idName.', ' ;
    $fields .= implode(", ", array_keys($this->content));
    $variables = (is_null($idName)) ? '' : ':'.$idName.', ' ;
    $variables .= implode(", ", array_keys($this->columns));
    $idType = (is_int($this->contentId)) ? \PDO::PARAM_INT : \PDO::PARAM_STR ;

    $statement = $this->pdo->prepare("INSERT INTO $table ($fields) VALUES ($variables)");

    if(!is_null($idName)) { $statement->bindParam(':'.$idName, $this->contentId, $idType); }

    foreach($this->columns as $columnName => $columnType) {
      $index = ltrim($columnName, ":");
      $statement->bindParam($columnName, $this->content[$index], $columnType);
    }

    return $statement->execute();

  }

  public function select(
    $idName = null,
    ?array $fields = null,
    ?string $where = null,
    ?array $keywords = null
  ): array {

    $table = $this->table;
    $fields = (is_null($fields)) ? '*' : implode(", ", array_keys($fields));

    $statement = $this->pdo->prepare("SELECT $fields FROM $table ");

    foreach($this->columns as $columnName => $columnType) {
      $index = ltrim($columnName, ":");
      $statement->bindParam($columnName, $this->content[$index], $columnType);
    }

  }

  public function update(string $id, array $params): bool;

  public function delete(string $id): bool;


}
