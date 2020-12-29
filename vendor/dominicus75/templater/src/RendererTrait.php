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
   * @var string Fully qualified path name of template directory
   *
   */
  private string $templateDirectory;


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
   * @var bool if this renderable template includes all sources, value of
   * this property true, false otherwise
   *
   */
  private bool $buildedUp = false;


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
    } else { $this->renderable = true; }

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
   * @parem string $marker in form '@@marker@@'
   * @param string $templateFile name of template (tpl) file, for example 'nav.tpl'
   * or 'page/read.tpl'
   * @throws \InvalidArgumentException if marker is not found
   * @throws \InvalidArgumentException if marker is already assigned
   *
   */
  public function assignSource(string $marker, string $sourceFile = ''): self {

    if($this->hasMarker($marker)){

      if(empty($sourceFile)) {
        $this->source = str_replace($marker, '', $this->source);
      } else {
        try {
          $source = new Source($this->templateDirectory.$sourceFile);
          $this->source = str_replace($marker, $source->getSource(), $this->source);
          $this->updateSources();
          $this->updateVariables();
        } catch(FileNotFoundException $e) { throw $e; }
      }

      $this->sources[$marker] = true;
      return $this;

    } else {
      throw new \InvalidArgumentException($marker.' is not found in template source');
    }

  }


  /**
   *
   * @parem string $marker in form '@@marker@@'
   * @param string $template rendered template, without varibles and markers
   * @throws \InvalidArgumentException if marker is not found
   * @throws \InvalidArgumentException if marker is already assigned
   *
   */
  public function assignTemplate(string $marker, string $template = ''): self {

    if($this->hasMarker($marker)){

      try {
        $this->source = str_replace($marker, $template, $this->source);
        $this->sources[$marker] = true;
        return $this;
      } catch(FileNotFoundException $e) { throw $e; }

    } else {
      throw new \InvalidArgumentException($marker.' is not found in template source');
    }

  }


  /**
   *
   * @param string $iterativeTemplateFile name of iterative template file (tpl) for example 'navItem.tpl'
   * @param string $marker in form '@@marker@@'
   * @param array $content
   *
   * @throws \Dominicus75\Templater\FileNotFoundException if the $iterativeTemplateFile
   * template file does not exists
   * @throws \InvalidArgumentException if marker is invalid or missing
   * @throws \InvalidArgumentException if either $content item is not an array
   *
   */
  public function assignTemplateIterator(
    string $iterativeTemplateFile,
    string $marker,
    array $content
  ): self {

    if($this->hasMarker($marker)){

      try {

        $iterator = new TemplateIterator(
          $this->templateDirectory.$iterativeTemplateFile,
          $content
        );

        $this->source = str_replace($marker, $iterator->render(), $this->source);
        $this->sources[$marker] = true;
        return $this;

      } catch(\InvalidArgumentException |
              \RuntimeException |
              \FileNotFoundException $e) { throw $e; }

    } else {
      throw new \InvalidArgumentException($marker.' is not found in template source');
    }

  }


  /**
   *
   * @param void
   * @return self
   * @throws \RuntimeException, if any template or variable is missing
   *
   */
  public function buildLayout(): self {

    $this->updateSources();

    foreach($this->sources as $marker => $template){
      if(!$template) { throw new \RuntimeException($marker.' template is missing'); }
    }

    $this->updateVariables();
    $this->buildedUp = true;
    return $this;

  }


  /**
   *
   * @param void
   * @return bool
   *
   */
  public function isRenderable(): bool {

    if(!$this->buildedUp) { return false; }
    if(empty($this->variables)) { return true; }
    foreach($this->variables as $variable) {
      if(is_null($variable)) { return false; }
    }
    return true;

  }


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
