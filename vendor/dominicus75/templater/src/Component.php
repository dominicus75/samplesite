<?php
/*
 * @file Component.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Component extends Source {

  use RendererTrait;

  /**
   *
   * @var string Fully qualified path name of template directory
   *
   */
  private string $templateDirectory;

  /**
   *
   * @var bool if $this->component includes all sources, value of
   * this property true, false otherwise
   *
   */
  private bool $buildedUp = false;


  /**
   *
   * @param string $templateDirectory Fully qualified path name
   * @param string $componentFile Name of componenet source file,
   * for example 'nav.tpl' or 'aside.tpl'
   *
   * @throws \Dominicus75\Templater\DirectoryNotFoundException
   * if given directory does not exists
   * @throws \Dominicus75\Templater\FileNotFoundException
   * if given file does not exists
   */
  public function __construct(
    string $templateDirectory,
    string $componentFile
  ){

    if(is_dir($templateDirectory)) {
      $this->templateDirectory = $templateDirectory;
      try {
        parent::__construct($this->templateDirectory.$componentFile);
      } catch(FileNotFoundException $e) { throw $e; }
    } else {
      throw new DirectoryNotFoundException($templateDirectory.' does not exists.');
    }

    $this->updateSources();
    $this->updateVariables();

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

    foreach($this->variables as $variable) {
      if(is_null($variable)) { return false; }
    }

    return true;

  }

  public function __get($name) { return $this->$name; }

}
