<?php
/*
 * @file Skeleton.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Skeleton extends Source {

  use RendererTrait;

  /**
   *
   * @var string Fully qualified path name of css directory
   *
   */
  private string $cssDirectory;


  /**
   *
   * @param string $templateDirectory Fully qualified path name
   * @param string $cssDirectory Fully qualified path name
   * @param string $skeletonFile Name of skeleton source file,
   * for example 'skeleton.html'
   *
   * @throws \Dominicus75\Templater\DirectoryNotFoundException
   * if given directory does not exists
   * @throws \Dominicus75\Templater\FileNotFoundException
   * if given skeleton file does not exists
   */
  public function __construct(
    string $templateDirectory,
    string $cssDirectory,
    string $skeletonFile
  ){

    if(is_dir($templateDirectory)) {
      $this->templateDirectory = $templateDirectory;
      try {
        parent::__construct($this->templateDirectory.$skeletonFile);
      } catch(FileNotFoundException $e) { throw $e; }
    } else {
      throw new DirectoryNotFoundException($templateDirectory.' does not exists.');
    }

    if(is_dir($cssDirectory)) {
      $this->cssDirectory = $cssDirectory;
    } else {
      throw new DirectoryNotFoundException($cssDirectory.' does not exists.');
    }

    $this->updateSources();
    $this->updateVariables();

  }


  /**
   *
   * @parem string $marker in form '@@marker@@'
   * @param string $cssFile name of template (tpl) file, for example 'common.css'
   * @throws \InvalidArgumentException if marker is not found
   * @throws \InvalidArgumentException if marker is already assigned
   *
   */
  public function assignCSS(string $marker, string $cssFile = ''): self {

    if($this->hasMarker($marker)){

      if(empty($cssFile)) {
        $this->source = str_replace($marker, '', $this->source);
      } else {
        try {
          $css = new Source($this->cssDirectory.$cssFile);
          $this->source = str_replace($marker, $css->getSource(), $this->source);
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


}
