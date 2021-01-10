<?php
/*
 * @file Page.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View\Admin;

use \Dominicus75\Config\Config;
use \Dominicus75\Templater\Exceptions\{
  DirectoryNotFoundException,
  FileNotFoundException,
  MarkerNotFoundException,
  NotRenderableException
};

class Login extends Dashboard
{

  /**
   * Constructor of class Admin.
   *
   * @return void
   */
  public function __construct()
  {

    $parameters['type']   = 'admin';
    $parameters['action'] = 'login';
    $parameters['script'] = false;

    try {
      parent::__construct($parameters);
    } catch(DirectoryNotFoundException | FileNotFoundException | MarkerNotFoundException $e) {
      echo $e->getMessage();
    }

  }

}
