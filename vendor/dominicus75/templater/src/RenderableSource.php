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
  public function __construct(string $tplFile, array $variables = [])
  {
    try {
      parent::__construct($tplFile);
      $this->initVariables();
      $this->setVariables($variables);
    } catch(\Exceptions\FileNotFoundException $e) { throw $e; }
  }


  /**
   *
   * This method extracts varible markers from source and initialize $this->varibles
   *
   * @param void
   * @return self
   *
   */
  protected function initVariables(): self {
    if(preg_match_all(Templater::MARKERS['variable'], $this->source, $matches)) {
      foreach($matches[0] as $marker){
        if(!array_key_exists($marker, $this->variables)) { $this->variables[$marker] = null; }
      }
    }
    return $this;
  }

  /**
   *
   * Determines if a variable is declared and is different than null
   *
   * @param string $marker in form '{{marker}}' (variable marker)
   * @return bool
   *
   */
  protected function issetVariable(string $marker): bool {
    return isset($this->variables[$marker]);
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
    } elseif(!$this->issetVariable($marker)) {
      $this->variables[$marker] = $value;
      return $this;
    } else {
      throw new Exceptions\VariableExistsException($marker.' has already value');
    }

  }

  /**
   *
   * @param array $variables The string variables belongs to this Renderable Source
   * in (string)'{{marker}}' => (string)'value' form
   * @return void
   * @throws \Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @throws \Dominicus75\Templater\Exceptions\VariableExistsException if marker has already value
   *
   */
  public function setVariables(array $variables = []): void {
    if(!empty($variables)) {
      foreach($variables as $marker => $value) {
        try {
          $this->bindValue($marker, $value);
        } catch(Exceptions\VariableExistsException | Exceptions\MarkerNotFoundException $e) { throw $e; }
      }
    }
  }

  /**
   *
   * This method update varibles array
   *
   * @param array $variables string variables belongs to this Renderable Source
   * in (string)'{{marker}}' => (string)'value' form
   *
   * @return self
   *
   */
  public function updateVariables(array $variables): void {
    foreach($variables as $name => $value) {
      if(!preg_match(Templater::MARKERS['variable'], $name)) {
        $marker = '{{'.$name.'}}';
        $variables[$marker] = $value;
        unset($variables[$name]);
      }
    }
    $this->variables = array_merge($this->variables, $variables);
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
   * @throws \Dominicus75\Templater\Exceptions\NotRenderableException
   * if this Renderable Source is not renderable yet
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

  /**
   *
   * @param void
   * @return string the rendered source
   * @throws \Dominicus75\Templater\Exceptions\NotRenderableException
   * if this Layout is not renderable yet
   *
   */
  public function display(): string {
    try {
      $this->render();
      return $this->getSource();
    } catch(NotRenderableException $e) { throw $e; }
  }

}
