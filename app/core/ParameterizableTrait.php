<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Core;

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
      foreach($parameters as $name => $value) { $this->setParameter($name, $value); }
    }
  }

  protected function hasParameter(string $name): bool {
    return array_key_exists($name, $this->parameters);
  }

  protected function setParameter(string $name, $value): void {
    if(!$this->hasParameter($name)) { $this->parameters[$name] = $value; }
  }

  protected function getParameter(string $name) {
    if($this->hasParameter($name)) {
      return $this->parameters[$name];
    } else { return null; }
  }

}
