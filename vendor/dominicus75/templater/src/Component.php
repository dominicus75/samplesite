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
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException
   * if given source file does not exists
   *
   */
  public function __construct(string $componentFile, array $sources = []){

    try {
      parent::__construct($componentFile);
      $this->setSources($sources);
    } catch(Exceptions\FileNotFoundException $e) { throw $e; }

  }

  /**
   *
   * This method extracts source markers from source and initialize $this->sources
   *
   * @param void
   * @return self
   *
   */
  protected function initSources(): self {
    if(preg_match_all(Templater::MARKERS['source'], $this->source, $matches)) {
      foreach($matches[0] as $marker) {
        if(!array_key_exists($marker, $this->sources)) { $this->sources[$marker] = null; }
      }
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
  public function assignText(string $marker, string $text = ''): void {

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
   * @param string $sourceFile Fully qualified path name of source file
   * @throws Exceptions\MarkerNotFoundException if marker is not found
   * @throws Exceptions\SourceExistsException if $this->sources[$marker] is already set
   * @throws Exceptions\FileNotFoundException if source file is not found
   *
   */
  public function assignSource(
    string $marker,
    string $sourceFile = ''
  ): self {

    if(!$this->hasMarker($marker)){
      throw new Exceptions\MarkerNotFoundException($marker.' is not found in this source');
    } elseif($this->sources[$marker]) {
      throw new Exceptions\SourceExistsException($marker.' is already set');
    } elseif(empty($sourceFile)) {
      $this->source = str_replace($marker, '', $this->source);
      $this->sources[$marker] = true;
      return $this;
    } else {
      try {
        $source = new Source($sourceFile);
        $this->source = str_replace($marker, $source->getSource(), $this->source);
        $this->initSources();
        $this->initVariables();
        $this->sources[$marker] = true;
        return $this;
      } catch(Exceptions\FileNotFoundException $e) { throw $e; }
    }

  }
  /**
   *
   * @parem string $marker in form '@@marker@@'
   * @param string $sourceFile Fully qualified path name of source file
   * @throws Exceptions\MarkerNotFoundException if marker is not found
   * @throws Exceptions\SourceExistsException if $this->sources[$marker] is already set
   * @throws Exceptions\FileNotFoundException if source file is not found
   *
   */
  public function assignRenderableSource(
    string $marker,
    string $sourceFile,
    array $variables
  ): self {

    if(!$this->hasMarker($marker)){
      throw new Exceptions\MarkerNotFoundException($marker.' is not found in this source');
    } elseif($this->sources[$marker]) {
      throw new Exceptions\SourceExistsException($marker.' is already set');
    } else {
      try {
        $source = new RenderableSource($sourceFile, $variables);
        $this->source = str_replace($marker, $source->display(), $this->source);
        $this->sources[$marker] = true;
        return $this;
      } catch(Exceptions\FileNotFoundException $e) { throw $e; }
    }

  }


  /**
   *
   * @param array  $sources The sources belongs to this Component
   * in [(string)'@@marker@@' => (string)'source'] form (source marker)
   * 'source' is fully qualified path name of source file
   * @return void
   * @throws Exceptions\MarkerNotFoundException if marker is not found
   * @throws Exceptions\SourceExistsException if $this->sources[$marker] is already set
   * @throws Exceptions\FileNotFoundException if source file is not found
   *
   */
  public function setSources(array $sources = []): void {
    if(!empty($sources)) {
      foreach($sources as $marker => $source) {
        try {
          $this->assignSource($marker, $source);
        } catch(Exceptions\FileNotFoundException |
                Exceptions\SourceExistsException |
                Exceptions\MarkerNotFoundException $e) { throw $e; }
      }
    } else { $this->initSources(); }
  }


  /**
   *
   * This method update varibles array
   *
   * @param array  $sources The sources belongs to this Component
   * in [(string)'@@marker@@' => (string)'source'] form (source marker)
   * 'source' is fully qualified path name of source file
   *
   * @return self
   *
   */
  public function updateSources(array $sources): self {
    $this->sources = array_merge($this->sources, $sources);
    return $this;
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
    string $marker,
    string $iterativeTemplateFile,
    array $content
  ): self {

    if($this->hasMarker($marker)){

      try {
        $repeater = new Repeater($iterativeTemplateFile, $content);
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
  public function isRenderable(): bool {
    if(parent::isRenderable()) {
      return !in_array(false, $this->sources, true);
    }
    return false;
  }

}
