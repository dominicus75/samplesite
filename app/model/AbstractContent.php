<?php
/*
 * @file AbstractContent.php
 * @package MVC
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;

abstract class AbstractContent
{

  protected PDO $pdo;
  protected string $table;
  protected string $primaryKey;
  protected ?string $url;
  protected array $content = [];
  protected array $columns = [];
  protected array $updated = [];


  public function __construct(
    \ArrayAccess $pdoConfig,
    ?string $url = null
  ){

    try {

      $this->pdo = PDO::getInstance($pdoConfig);
      $this->url = $url;

      $this->table      = "contents";
      $this->primaryKey = "id";
      $this->columns = [
        $this->primaryKey => [":".$this->primaryKey, \PDO::PARAM_INT],
        "type" => [":type", \PDO::PARAM_INT],
        "category" => [":category", \PDO::PARAM_INT],
        "url" => [":url", \PDO::PARAM_STR],
        "author" => [":author", \PDO::PARAM_INT],
        "title" => [":title", \PDO::PARAM_STR],
        "description" => [":description", \PDO::PARAM_STR],
        "body" => [":body", \PDO::PARAM_STR],
        "created" => [":created", \PDO::PARAM_INT],
        "updated" => [":updated", \PDO::PARAM_INT]
      ];

      if(!is_null($url)) {
        $this->setContent($this->select());
      }


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


  public function setContent(array $content): void {

    foreach($content as $fieldName => $value) {
      try {
        $this->setField($fieldName, $value);
      } catch(InvalidFieldNameException $e) { throw $e; }
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

      if($field === $this->primaryKey || $field === 'url') {
        throw new InvalidFieldNameException("The primary and unique keys are immutable!");;
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
      if($name == $this->primaryKey) { continue; }
      $fields .= '`'.$name.'`, ';
      $variables .= $properties[0].', ';
    }
    $fields = rtrim($fields, ', ');
    $variables = rtrim($variables, ', ');

    $sql= "INSERT INTO `".$this->table."` ($fields) VALUES ($variables)";

    $statement = $this->pdo->prepare($sql);

    foreach($this->columns as $name => $properties) {
      if($name == $this->primaryKey) { continue; }
      $statement->bindParam($properties[0], $this->content[$name], $properties[1]);
    }

    return $statement->execute();

  }


  public function select(array $params = []): array {

    if(empty($params)){
      $sql  = "SELECT * FROM `".$this->table;
      $sql .= "` WHERE `url` = ".$this->columns['url'][0];
      $statement = $this->pdo->prepare($sql);
      $statement->bindParam($this->columns['url'][0],
                            $this->url,
                            $this->columns['url'][1]);
      $statement->execute();
      $result = $statement->fetch(PDO::FETCH_ASSOC);
      if($result) {
        return $result;
      } else { return []; }
    }

  }

  public function update(): bool {

    if(empty($this->updated)) { return false; }

    $sql  = "UPDATE `".$this->table."` SET ";

    foreach($this->updated as $name => $value) {
      $sql .= "`".$name."` = ".$this->columns[$name][0].", ";
    }

    $sql = rtrim($sql, ', ');
    $sql .= " WHERE `url` = ".$this->columns['url'][0];

    $statement = $this->pdo->prepare($sql);

    foreach($this->updated as $name => $value) {
      $statement->bindParam($this->columns[$name][0], $value, $this->columns[$name][1]);
    }

    return $statement->execute();

  }

  public function delete(): bool {

    if(empty($this->contentID)) { return false; }

    $sql  = "DELETE FROM `".$this->table;
    $sql .= "` WHERE `url` = ".$this->columns['url'][0];
    $statement = $this->pdo->prepare($sql);
    $statement->bindParam($this->columns['url'][0],
                          $this->content['url'],
                          $this->columns['url'][1]);

    return $statement->execute();

  }


}
