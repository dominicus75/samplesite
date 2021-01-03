<?php
/*
 * @file Component.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Component extends RenderableSource {

  /**
   *
   * @var string Fully qualified path name of template directory
   *
   */
  protected string $templateDirectory;

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
   * @param string $templateDirectory Fully qualified path name of directory
   * @param string $componentFile Fully qualified path name of source file,
   *
   * @throws \Dominicus75\Templater\Exceptions\DirectoryNotFoundException
   * if given directory does not exists
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException
   * if given source file does not exists
   *
   */
  public function __construct(
    string $componentFile,
    string $templateDirectory = ''
  ){

    if(!empty($templateDirectory)) {
      if(is_dir($templateDirectory)) {
        $this->templateDirectory = $templateDirectory;
      } else {
        throw new Exceptions\DirectoryNotFoundException($templateDirectory.' does not exists.');
      }
    } else { $this->templateDirectory = Templater::DIR; }

    $componentFile = $this->templateDirectory.$componentFile;

    try {
      parent::__construct($componentFile);
      $this->updateSources();
    } catch(Exceptions\FileNotFoundException $e) { throw $e; }

  }

  /**
   *
   * This method extracts source markers from source
   * and update sources array
   *
   * @param void
   * @return self
   *
   */
  protected function updateSources(): self {

    if(preg_match_all(Templater::MARKERS['source'], $this->source, $matches)) {
      foreach($matches[0] as $marker){ $this->sources[$marker] = false; }
    }
    return $this;

  }


  /**
   *
   * @parem string $marker in form '@@marker@@'
   * @param string $text without varibles and markers
   * @throws Exceptions\MarkerNotFoundException if marker is not found
   * @throws Exceptions\VariableExistsException if $this->sources[$marker] is already set
   *
   */
  protected function assignText(string $marker, string $text = ''): void {

    if(!$this->hasMarker($marker)){
      throw new Exceptions\MarkerNotFoundException($marker.' is not found in this source');
    } elseif($this->sources[$marker]) {
      throw new Exceptions\VariableExistsException($marker.' is already set');
    } else {
      $this->source = str_replace($marker, $text, $this->source);
      $this->sources[$marker] = true;
    }

  }


  /**
   *
   * @parem string $marker in form '@@marker@@'
   * @param string $sourceFile Fully qualified path name of template source file (tpl)
   * @throws Exceptions\MarkerNotFoundException if marker is not found
   * @throws Exceptions\VariableExistsException if $this->sources[$marker] is already set
   *
   */
  public function assignSource(
    string $marker,
    string $templateDirectory,
    string $sourceFile = ''
  ): self {

    if(!$this->hasMarker($marker)){
      throw new Exceptions\MarkerNotFoundException($marker.' is not found in this source');
    } elseif($this->sources[$marker]) {
      throw new Exceptions\VariableExistsException($marker.' is already set');
    } elseif(empty($sourceFile)) {
      $this->source = str_replace($marker, '', $this->source);
      $this->sources[$marker] = true;
      return $this;
    } else {
      $sourceFile = empty($templateDirectory)
        ? $this->templateDirectory.$sourceFile
        : $templateDirectory.$sourceFile;
      try {
        $source = new Source($sourceFile);
        $this->source = str_replace($marker, $source->getSource(), $this->source);
        $this->updateSources();
        $this->updateVariables();
        $this->sources[$marker] = true;
        return $this;
      } catch(Exceptions\FileNotFoundException $e) { throw $e; }
    }

  }


  /**
   *
   * @param string $iterativeTemplateFile name of iterative source file (tpl) for example 'menu_item.tpl'
   * @param string $marker in form '@@marker@@'
   * @param array $content
   *
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException if the $iterativeTemplateFile
   * source file does not exists
   * @throws \Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @throws \InvalidArgumentException if either $content item is not an array
   * @throws \Dominicus75\Templater\Exceptions\VariableExistsException
   * @throws \Dominicus75\Templater\Exceptions\NotRenderableException
   *
   */
  public function assignRepeater(
    string $iterativeTemplateFile,
    string $marker,
    array $content
  ): self {

    if($this->hasMarker($marker)){

      try {
        $repeater = new Repeater($this->templateDirectory.$iterativeTemplateFile, $content);
        $this->source = str_replace($marker, $repeater->getSource(), $this->source);
        $this->sources[$marker] = true;
        return $this;
      } catch(
        \InvalidArgumentException |
        Exceptions\FileNotFoundException |
        Exceptions\MarkerNotFoundException |
        Exceptions\VariableExistsException |
        Exceptions\NotRenderableException $e) { throw $e; }

    } else {
      throw new Exceptions\MarkerNotFoundException($marker.' is not found in this source');
    }

  }

  /**
   *
   * @param void
   * @return bool
   *
   */
  public function isComplete(): bool {
    return !in_array(false, $this->sources, true);
  }


  /**
   *
   * @param void
   * @return string
   * @throws \Dominicus75\Templater\Exceptions\NotRenderableException
   * if this Renderable Source is not renderable yet
   *
   */
  public function render(): void {

    if($this->isComplete()) {
      parent::render();
    } else {
      throw new Exceptions\NotRenderableException('This source is not renderable yet.');
    }

  }

}
