<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;

use \Dominicus75\Config\Config;
use \Dominicus75\Model\AbstractModel;

class Message extends AbstractModel
{

  /**
   * Constructor of class Fault.
   *
   * @return void
   */

  public function __construct(Config $pdoConfig, string $table, array $message = []){

    try {
      parent::__construct($pdoConfig, $table);
      if(!empty($message)) { $this->content = $message; }
    } catch(\PDOException $e) { throw $e; }

  }


  public function create(array $content): bool {}

  public function read(array $url): array {
    if(empty($this->content)) {
      $this->content = $this->table->select(['url', $url['cid']]);
    }
    return $this->content;
  }

  public function edit(array $url, array $updated): bool {}
  public function delete(array $url): bool {}

}
