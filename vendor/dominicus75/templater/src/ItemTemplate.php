<?php
/*
 * @file ItemTemplate.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class ItemTemplate extends Template
{

  /**
   *
   * @var array The string variables belongs to this Template
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
   * @param string $url Fully qualified path name of iterative template file (tpl|html)
   *
   * @throws \Dominicus75\Templater\FileNotFoundException if the template
   * file does not exists
   *
   */
  public function __construct(string $itemTemplateUrl){

    try {
      parent::__construct($itemTemplateUrl);
    } catch(FileNotFoundException $e) { throw $e; }


    if(preg_match_all(Skeleton::MARKERS['variable'], $this->source, $matches)) {
      foreach($matches[0] as $marker){ $this->variables[$marker] = null; }
    } else {
      throw new \RuntimeException('No variable markers found in this template file');
    }

  }


  /**
   *
   * @param string $marker in form '{{marker}}'
   * @param string $value
   * @throws \InvalidArgumentException if marker is not found
   * @throws \InvalidArgumentException if marker has already value
   *
   */
  private function bindValue(string $marker, string $value): self {

    if(array_key_exists($marker, $this->variables)){
      if(is_null($this->variables[$marker])) {
        $this->variables[$marker] = $value;
        return $this;
      } else {
        throw new \InvalidArgumentException($marker.' has already value');
      }
    } else {
      throw new \InvalidArgumentException($marker.' is not found in this template file');
    }

  }


  /**
   *
   * @param array $variables The string variables belongs to this ItemTemplate
   * in string $marker => string $value form
   * @return void
   * @throws \InvalidArgumentException if marker is not found
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
   * @return string
   * @throws \RuntimeException if this ItemTemplate is not renderable
   *
   */
  public function render(): string {

    if($this->renderable) {

      return str_replace(
        array_keys($this->variables),
        array_values($this->variables),
        $this->source
      );

    } else {
      throw new \RuntimeException('This ItemTemplate is not renderable yet.');
    }

  }

}
