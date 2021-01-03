<?php
/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class RenderableSource extends Source
{

  /**
   *
   * @var array variables belongs to this source
   * in (string)'{{marker}}' => (bool)false|true form
   * It is true, if varibles has insered, false otherwise
   *
   */
  protected array $variables = [];

  /**
   * Constructor of class RenderableSource.
   * @param string $tplFile Fully qualified path name of template file (tpl|html)
   *
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException if the template
   * file does not exists
   *
   */
  public function __construct(string $tplFile)
  {
    try {
      parent::__construct($tplFile);
      $this->updateVariables();
    } catch(\Exceptions\FileNotFoundException $e) { throw $e; }
  }


  /**
   *
   * @param string $marker in form '%%marker%%' or '{{marker}}' or '@@marker@@'
   * what we are looking for
   * @return bool true, if marker was found in $this->source, false otherwise
   *
   */
  protected function hasMarker(string $marker): bool {
    return (bool)preg_match("/".$marker."/i", $this->source);
  }

  /**
   *
   * This method extracts varible markers from source
   * and update varibles array
   *
   * @param void
   * @return self
   *
   */
  protected function updateVariables(): self {

    if(preg_match_all(Templater::MARKERS['variable'], $this->source, $matches)) {
      foreach($matches[0] as $marker){ $this->variables[$marker] = null; }
    }
    return $this;
  }

  /**
   *
   * @param string $marker in form '{{marker}}'
   * @param string $value
   * @throws \Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @throws \Dominicus75\Templater\Exceptions\VariableExistsException if marker has already value
   *
   */
  public function bindValue(string $marker, ?string $value): self {

    if(!$this->hasMarker($marker)) {
      throw new Exceptions\MarkerNotFoundException($marker.' is not found in this source');
    } elseif(!$this->variables[$marker]) {
      $this->variables[$marker] = $value;
      return $this;
    } else {
      throw new Exceptions\VariableExistsException($marker.' has already value');
    }

  }

  /**
   *
   * @param array $variables The string variables belongs to this IterativeTemplate
   * in string $marker => string $value form
   * @return void
   * @throws \Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @throws \Dominicus75\Templater\Exceptions\VariableExistsException if marker has already value
   *
   */
  public function setVariables(array $variables): void {

    foreach($variables as $marker => $value) {
      try {
        $this->bindValue($marker, $value);
      } catch(Exceptions\VariableExistsException | Exceptions\MarkerNotFoundException $e) { throw $e; }
    }

  }

  /**
   *
   * @param void
   * @return bool
   *
   */
  public function isRenderable(): bool {
    return !in_array(null, $this->variables, true);
  }

  /**
   *
   * @param void
   * @return void
   * @throws \Dominicus75\Templater\Exceptions\NotRenderableException if this Renderable Source is not renderable
   *
   */
  public function render(): void {

    if($this->isRenderable()) {

      $this->source = str_replace(
        array_keys($this->variables),
        array_values($this->variables),
        $this->source
      );

    } else {
      throw new Exceptions\NotRenderableException('This source is not renderable yet.');
    }

  }

}
