<?php
/*
 * @file RendererTrait.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;

trait RendererTrait
{

  /**
   *
   * @var array The sources belongs to this skeleton
   * in string @@marker@@ => bool false|true form
   * It is true, if source was assigned, false otherwise
   *
   */
  protected array $sources = [];

  /**
   *
   * @var array The string variables belongs to this Renderable Template
   * in string $marker => string $value form
   *
   */
  protected array $variables = [];

  /**
   *
   * @var bool This template is rendereble or not
   *
   */
  protected bool $renderable = false;


  /**
   *
   * This method extracts source markers from source
   * and update sources array
   *
   * @param void
   * @return void
   *
   */
  private function updateSources(): void {

    if(preg_match_all(Templater::MARKERS['source'], $this->source, $matches)) {
      foreach($matches[0] as $marker){ $this->sources[$marker] = false; }
    }

  }

  /**
   *
   * This method extracts varible markers from source
   * and update varibles array
   *
   * @param void
   * @return void
   *
   */
  private function updateVariables(): void {

    if(preg_match_all(Templater::MARKERS['variable'], $this->source, $matches)) {
      foreach($matches[0] as $marker){ $this->variables[$marker] = null; }
    }

  }

  /**
   *
   * @param string $marker what we are looking for
   * @return bool true, if marker was found, false otherwise
   *
   */
  public function hasMarker(string $marker): bool {
    if(strpos($this->source, $marker) !== false) {
      return true;
    }
    return false;
  }

  /**
   *
   * @param string $marker in form '{{marker}}'
   * @param string $value
   * @throws \InvalidArgumentException if marker is not found
   * @throws \InvalidArgumentException if marker has already value
   *
   */
  public function bindValue(string $marker, ?string $value): self {

    if(!isset($this->variables[$marker])) {
      $this->variables[$marker] = $value;
      return $this;
    } else {
      throw new \InvalidArgumentException($marker.' has already value');
    }

  }


  /**
   *
   * @param array $variables The string variables belongs to this IterativeTemplate
   * in string $marker => string $value form
   * @return void
   * @throws \InvalidArgumentException if marker has already value
   *
   */
  public function setVariables(array $variables): void {

    foreach($variables as $marker => $value) {
      try {
        $this->bindValue($marker, $value);
      } catch(\InvalidArgumentException $e) { throw $e; }
    }

    $this->renderable = true;

  }


  /**
   *
   * @param void
   * @return bool
   *
   */
  abstract public function isRenderable(): bool;


  /**
   *
   * @param void
   * @return string
   * @throws \RuntimeException if this Renderable Template is not renderable
   *
   */
  public function render(): string {

    if($this->isRenderable()) {

      return str_replace(
        array_keys($this->variables),
        array_values($this->variables),
        $this->source
      );

    } else {
      throw new \RuntimeException('This template is not renderable yet.');
    }

  }

}
