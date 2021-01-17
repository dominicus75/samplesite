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

class View extends \Application\View\Dashboard
{

  /**
   * Constructor of class View.
   *
   * @return void
   */
  public function __construct(array $parameters)
  {
    try {
      $parameters['meta']   = $parameters['type'] == 'message' ? false : true;
      $parameters['aside']  = $parameters['type'] == 'message' ? false : true;
      $parameters['user']   = $parameters['type'] == 'message' ? false : true;
      $parameters['script'] = false;
      parent::__construct($parameters);
    } catch(DirectoryNotFoundException | FileNotFoundException | MarkerNotFoundException $e) {
      echo $e->getMessage();
    }

  }

}
