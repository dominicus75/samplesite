<?php
/*
 * @file Page.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View\Visitor;

use \Dominicus75\Config\Config;
use \Dominicus75\Templater\Exceptions\{
  DirectoryNotFoundException,
  FileNotFoundException,
  MarkerNotFoundException,
  NotRenderableException
};

class View extends Site
{

  /**
   * Constructor of class Page.
   *
   * @return void
   */
  public function __construct(string $type)
  {

    $parameters['type']   = $type;
    $parameters['meta']   = $type == 'message' ? false : true;
    $parameters['aside']  = $type == 'message' ? false : true;
    $parameters['script'] = false;

    try {
      parent::__construct($parameters);
    } catch(DirectoryNotFoundException | FileNotFoundException | MarkerNotFoundException $e) {
      echo $e->getMessage();
    }

  }

}
