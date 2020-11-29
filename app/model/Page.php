<?php
/*
 * @file Page.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;


class Page extends \Dominicus75\MVC\AbstractModel
{


  public function __construct(
    \ArrayAccess $pdoConfig,
    $contentID = ''
  ){

    parent::__construct($pdoConfig, $contentID);

    $this->table      = "page";
    $this->primaryKey = "pid";
    $this->columns = [
      $this->primaryKey => [":".$this->primaryKey, \PDO::PARAM_STR],
      "title" => [":title", \PDO::PARAM_STR],
      "keywords" => [":keywords", \PDO::PARAM_STR],
      "description" => [":description", \PDO::PARAM_STR],
      "body" => [":body", \PDO::PARAM_STR]
    ];
    $this->setContent();

  }


}
