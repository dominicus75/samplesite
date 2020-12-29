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


  public function render(): string {

    if($this->isRenderable()) {
      return $this->source;
    } else {
      throw new \RuntimeException('This template is not renderable yet.');
    }

  }

  public function __get($name) { return $this->$name; }

}
