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

class Dashboard extends \Application\Core\AbstractController
{

  /**
   *
   * @param Router\Route $route current route instance
   * @param array $content (optional)
   *
   *
   */
  public function __construct(Route $route, Request $request){
    $parameters['content_type']  = 'admin';
    $parameters['content_table'] = 'admins';
    parent::__construct($route, $parameters, $request);
  }

  }


  public function view(): string {}


}
