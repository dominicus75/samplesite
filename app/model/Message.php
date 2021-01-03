<?php
/*
 * @file Fault.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;

use \Dominicus75\Core\{Config as Config, Model\AbstractModel as AbstractModel};

class Fault extends AbstractModel
{

  /**
   * Constructor of class Fault.
   *
   * @return void
   */

  public function __construct(Config $pdoConfig, string $table, array $message = []){

    try {
      parent::__construct($pdoConfig, $table, $message);
    } catch(\PDOException $e) { throw $e; }

  }


  public function create(): bool {}

  public function read(array $url): array {
    $content = $this->table->select(['url', $url['cid']]);
    $content['message'] = $this->hasParameter('message') ? PHP_EOL."        <p>".$this->getParameter('message')."</p>" : '';
    return $content;
  }

  public function edit(array $url): bool {}
  public function delete(array $url): bool {}

}
