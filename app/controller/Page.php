<?php
/*
 * @file Page.php
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

class Page extends \Application\Core\Site
{

  public function __construct(
    Route $route
  ){

    try {
      parent::__construct($route);
    } catch(\PDOException | InvalidFieldNameException $e) {
      echo $e->getMessage();
    }

  }


  public function create(): string {}
  public function view(): string {}
  public function edit(): string {}
  public function delete(): string {}


}
