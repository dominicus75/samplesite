<?php
/*
 * @package Core
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Model;

abstract class AbstractModel
{

  protected Table $table;
  protected array $content = [];
  protected array $updated = [];

  /**
   *
   * @var array optional parameters
   *
   */
  protected array $parameters = [];

  protected function __construct(
    $confOrInstance,
    string $table,
    array $parameters = []
  ){

    try {
      $this->table = new Table($confOrInstance, $table);
      $this->parameters = $parameters;
    } catch(\PDOException $pdoe) { throw $pdoe; }

  }


  protected function hasColumn($field): bool {
    return $this->table->hasColumn($field);
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

      if($field === $this->table->getPrimaryKey()) {
        throw new InvalidFieldNameException("The primary key is immutable!");;
      }

      $this->updated[$field] = $value;

    } else { throw new InvalidFieldNameException("The $field field is not exists"); }

  }


  public function getTableName(): string { return $this->table->getTable(); }


  public function getContent(): array {

    if(!empty($this->content)) {
      return $this->content;
    } else {
      throw new ContentNotFoundException();
    }

  }

  abstract public function create(array $content): bool;
  abstract public function read(array $url): array;
  abstract public function edit(array $url, array $updated): bool;
  abstract public function delete(array $url): bool;

}
