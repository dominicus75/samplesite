<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Http\Request;
use \Dominicus75\Config\Config;
use \Dominicus75\Router\Route;
use \Dominicus75\Model\{
  ContentNotFoundException,
  InvalidFieldNameException,
  InvalidStatementException
};

class Dashboard
{

  public function __construct(
    Route $route
  ){

    try {
      parent::__construct($route, $request);
    } catch(\PDOException | InvalidFieldNameException $e) {
      new Fault(500, $e->getMessage());
    }

  }


  public function view(): string {}


}
