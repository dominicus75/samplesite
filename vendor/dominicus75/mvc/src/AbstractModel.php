<?php
/*
 * @file AbstractModel.php
 * @package MVC
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\MVC;

abstract class AbstractModel implements ModelInterface
{

  protected \PDO $pdo;
  protected string $table;
  protected ?string $contentId;
  protected ?array $content;
  protected ?array $columns;


  public function __construct(
    \PDO $connection,
    string $tableName,
    $contentId = null,
    ?array $content = null
  ){

    $this->pdo       = $connection;
    $this->table     = $tableName;
    $this->contentId = $contentId;
    $this->setContent();

  }

  public function getTableName(): string { return $this->table; }

  public function getContentId() {
    return $this->contentId;
  }

  public function setContent(?array $content = null): void {

     if(is_null($this->content) && !is_null($content)) {

        $this->content = $content;

        foreach($this->content as $key => $value){

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

          $this->columns[$columnName] = (int)$columnType

        }

     } else { $this->content = null; }

  }

  public function getContent(): ?array { return $this->content; }

  public function insert(array $content): bool {

    if(is_null($this->content)) { return false; }

    $table = $this->table;
    $fields = implode(", ", array_keys($this->content));
    $variables = implode(", ", array_keys($this->columns));

    $statement = $this->pdo->prepare("INSERT INTO $table ($fields) VALUES ($variables)");

    foreach($this->columns as $columnName => $columnType) {
      $index = ltrim($columnName, ":");
      $statement->bindParam($columnName, $this->content[$index], $columnType);
    }

    return $statement->execute();

  }

  public function select(string $id, array $params): array;

  public function update(string $id, array $params): bool;

  public function delete(string $id): bool;


}
