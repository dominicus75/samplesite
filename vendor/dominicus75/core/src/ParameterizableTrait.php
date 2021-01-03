<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Core;

trait ParameterizableTrait
{

  /**
   *
   * @var array optional parameters
   *
   */
  protected array $parameters = [];


  protected function setParameters(array $parameters): void {

    if(!empty($parameters)) {
      foreach($parameters as $name => $value) {
        if(!is_null($value)) { $this->parameters[$name] = $value; }
      }
    }

  }

  protected function hasParameter($name): bool {
    return array_key_exists($name, $this->parameters);
  }

  protected function getParameter($name) {
    if($this->hasParameter($name)) {
      return $this->parameters[$name];
    } else { return false; }
  }

}
