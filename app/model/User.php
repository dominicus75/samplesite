<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;

use \Dominicus75\Config\Config;
use \Dominicus75\Model\AbstractModel;

class User extends AbstractModel
{

  public function __construct(Config $pdoConfig, string $table){

    try {
      parent::__construct($pdoConfig, $table);
    } catch(\PDOException $e) { throw $e; }

  }


  public function create(): bool {

  }

  public function read(): array {

  }

  public function edit(): bool {

  }

  public function delete(): bool {

  }

}
