<?php
/*
 * @file IterativeTemplate.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class IterativeTemplate extends Template
{

  use RendererTrait;

  /**
   *
   * @param string $url Fully qualified path name of iterative template file (tpl|html)
   *
   * @throws \Dominicus75\Templater\FileNotFoundException if the template
   * file does not exists
   *
   */
  public function __construct(string $iterativeTemplateUrl){

    try {
      parent::__construct($iterativeTemplateUrl);
      $this->extractVariableMarkers();
    } catch(FileNotFoundException $e) { throw $e; }

  }


  /**
   *
   * @param void
   * @return bool
   *
   */
  public function isRenderable(): bool { return $this->renderable; }


}
