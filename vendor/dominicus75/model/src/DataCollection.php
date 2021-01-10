<?php
/*
 * @package Model
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Model;

class DataCollection
{

  /**
   *
   * @var Table what stores this entity
   *
   */
  protected Table $table;

  /**
   *
   * @var array the data collection
   *
   */
  protected array $collection;

  /**
   *
   * @param string $name entity's name
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
    string $table,
    $confOrInstance,
    array $selectParameters = []
  ) {

    try {
      $this->table = new Table($confOrInstance, $table);
      $entities = $this->table->select(
        $selectParameters[0],
        $selectParameters[1],
        $selectParameters[2],
        true
      );
      foreach($entities as $entity) { $this->collection[] = $entity; }
    } catch(\PDOException $pdoe) { throw $pdoe; }

  }



}
