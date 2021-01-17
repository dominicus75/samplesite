<?php
/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View;

use \Dominicus75\Config\Config;
use \Dominicus75\Templater\Exceptions\{
  DirectoryNotFoundException,
  FileNotFoundException,
  MarkerNotFoundException,
  NotRenderableException
};

class Entrance extends \Dominicus75\Templater\Layout
{

  /**
   * Constructor of class Entrance.
   * @throws Dominicus75\Templater\Exceptions\FileNotFoundException if $sourceFile does not exists
   * @throws Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @return void
   */
  public function __construct(array $parameters, array $variables = [])
  {

    try {
      parent::__construct(new Config('entrance_view'), ETPL, 'entrance.html');
      $this->assignBufferedComponent('%%head%%');
      $this->assignComponent('%%form%%', ['file' => $parameters['action'].'.tpl']);
      $this->bindValue('{{background}}', '/images/dubai-city.jpg');
    } catch(DirectoryNotFoundException | FileNotFoundException | MarkerNotFoundException $e) {
      echo $e->getMessage();
    }

  }

}
