<?php
/*
 * @package Model
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Model;

class Entity implements EntityInterface
{

  /**
   *
   * @var string  this entity's name
   *
   */
  protected string $name;

  /**
   *
   * @var Table what stores this entity
   *
   */
  protected Table $table;

  /**
   *
   * @var array properties of this entity in form ['name' => value]
   *
   */
  protected array $properties = [];

  /**
   *
   * @var array only updated properties
   *
   */
  protected array $updated = [];


  /**
   *
   * @param string $name this entity's name
   * @param string $table table's name what stores this entity
   * @param $confOrInstance an instance of \Dominicus75\Config\Config
   * or an instance of \Dominicus75\Model\PDO
   * @param array $selectParameters (optional) parameters of EntityInterface::selectData()
   * all elements of $selectParameters must be an array!
   * @return self
   * @throws \PDOException if
   * - $table is not found in this database
   * - current PDO driver is not supported (now only mysql supported yet)
   * - PDOStatement::fetchAll() or execute() returns with false
   *
   */
  public function __construct(
    string $name,
    string $table,
    $confOrInstance,
    array $selectParameters = []) {

    try {
      $this->name  = $name;
      $this->table = new Table($confOrInstance, $table);
      if(empty($selectParameters)) {
        $this->initProperties();
      } else {
        switch(count($selectParameters)) {
          case 1: $this->properties = $this->selectData($selectParameters[0], [], [], false);
          break;
          case 2: $this->properties = $this->selectData($selectParameters[0], $selectParameters[1], [], false);
          break;
          case 3: $this->properties = $this->selectData($selectParameters[0], $selectParameters[1], $selectParameters[2], false);
          break;
          case 4: $this->properties = $this->selectData($selectParameters[0], $selectParameters[1], $selectParameters[2], false);
          break;
        }
      }

    } catch(\PDOException $pdoe) { throw $pdoe; }

  }


  /**
   *
   * @param void
   * @return \Dominicus75\Model\PDO a singleton instance of PDO
   *
   */
  public function getDatabase(): PDO { return $this->table->getDatabase(); }


  /**
   *
   * @param void
   * @return void
   *
   */
  private function initProperties(): void {
    foreach($this->table->getColumns() as $name => $properties) {
      $this->properties[$name] = null;
    }
  }


  /**
   *
   * @param string $name name of the requested property
   * @return bool Returns true if $name exists, false otherwise
   *
   */
  public function hasProperty(string $name): bool {
    return array_key_exists($name, $this->properties);
  }


  /**
   *
   * @param string $name name of the requested property
   * @return bool Returns true if $name property has value, false otherwise
   *
   */
  public function issetProperty(string $name): bool {
    return isset($this->properties[$name]);
  }


  /**
   *
   * @param string $name name of the property to set
   * @param scalar $value
   * @throws InvalidPropertyNameException if property is primary key or it already has value
   * @return self
   *
   */
  public function setProperty(string $name, $value): self {
    if(!$this->hasProperty($name)) {
      throw new InvalidPropertyNameException("The $name property is not exists");
    } elseif($name === $this->table->getPrimaryKey() && $this->table->isPrimaryAutoIncrement()) {
      throw new InvalidPropertyNameException("$name is a primary key and it has auto_increment attribute!");;
    } elseif(!$this->issetProperty($name)) {
      $this->properties[$name] = $value;
      return $this;
    } else { throw new InvalidPropertyNameException("The $name property already has value"); }
  }


  /**
   *
   * @param string $name name of the property to update
   * @param scalar $value
   * @return self
   * @throws InvalidPropertyNameException if property is a primary key, or it not exists
   *
   */
  public function updateProperty(string $name, $value): self {
    if(!$this->hasProperty($name)) {
      throw new InvalidPropertyNameException("The $name property is not exists");
    } elseif($name === $this->table->getPrimaryKey() && $this->table->isPrimaryAutoIncrement()) {
      throw new InvalidPropertyNameException("$name is a primary key and it is immutable!");
    } else {
      $this->updated[$name] = $value;
      return $this;
    }
  }


  /**
   *
   * @param string $name name of the requested property
   * @return scalar
   *
   */
  public function getProperty(string $name) {
    return $this->hasProperty($name) ? $this->properties[$name] : false;
  }

  /**
   *
   * @param array $properties multi-properties in [$name => $value] form
   * @return void
   * @throws InvalidPropertyNameException if a property is primary key or it already has value
   *
   */
  public function setProperties(array $properties): void {
    try {
      foreach($properties as $name => $value) { $this->setProperty($name, $value); }
    } catch(InvalidPropertyNameException $e) { throw $e; }
  }


  /**
   *
   * @param array $properties multi-properties in [$name => $value] form
   * @return void
   * @throws InvalidPropertyNameException if a property is a primary key, or it not exists
   *
   */
  public function updateProperties(array $properties): void {
    foreach($properties as $name => $value) { $this->updateProperty($name, $value); }
  }


  /**
   *
   * @param void
   * @return array
   *
   */
  public function getProperties(): array { return $this->properties; }


  /**
   *
   * @param void
   * @return array
   *
   */
  public function getUpdated(): array { return $this->updated; }


  /**
   * It is used to insert a new record in this table
   *
   * @param array $content in 'key' => 'value' form
   * (e. g. 'url' => 'aboutus', 'title' => 'About us')
   * @return bool Returns true on success or false on failure
   *
   */
  public function insertData(): bool { return $this->table->insert($this->properties); }


  /**
   * It is used to select data from this table
   *
   * @param array $key
   * $key[0] is key name (e. g. 'id')
   * $key[1] is value belongs to key (e. g. '1234' or 'contact')
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
  public function selectData(array $key, array $fields, array $furtherConditions, bool $fetchAll = false): array {
    try {
      return $this->table->select($key, $fields, $furtherConditions, $fetchAll);
    } catch(InvalidFieldNameException $e) { throw $e; }
  }


  /**
   * It is used to modify an existing record in table
   *
   * @param array $content in 'key' => 'value' form
   * (e. g. 'url' => 'aboutus', 'title' => 'About us')
   * @param string $key [optional, defeult is the primary key]
   * the updatable row's key
   * @return bool Returns true on success or false on failure
   *
   */
  public function updateData(string $key = ''): bool {
    return $this->table->insert($this->updated, $key);
  }


  /**
   * It is used to delete an existing record in table
   *
   * @param array $key the deletable row's key (key name and value)
   * $key[0] is key name (e. g. 'id')
   * $key[1] is value belongs to key (e. g. '1234' or 'contact')
   * @return bool Returns true on success or false on failure
   *
   */
  public function deleteData(array $key): bool { return $this->table->delete($key); }


}
