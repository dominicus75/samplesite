<?php
/*
 * @file Article.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Http\{Request, Response};
use \Dominicus75\Core\{
  AbstractController,
  Config as Config,
  Router\Route as Route,
  Model\ContentNotFoundException as ContentNotFoundException,
  Model\InvalidFieldNameException as InvalidFieldNameException,
  Model\InvalidStatementException as InvalidStatementException
};

class AJAX extends AbstractController
{

  public function __construct(
    Route $route,
    Request $request
  ){

    try {
      parent::__construct($route, $request);
    } catch(\PDOException | InvalidFieldNameException $e) {
      new Fault(500, $e->getMessage());
    }

  }


  public function get(): string {}
  public function post(): string {}


}
