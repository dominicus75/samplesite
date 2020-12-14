<?php
/*
 * @file Skeleton.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Skeleton extends Template {

  use RendererTrait;

  /**
   *
   * @var string Fully qualified path name of template directory
   *
   */
  private string $templateDirectory;

  /**
   *
   * @var array The templates belongs to this skeleton
   * in string @@marker@@ => Template $template form
   *
   */
  protected array $templates = [];

  /**
   *
   * @var bool if $this->skeleton includes all template sources, value of
   * this property true, false otherwise
   *
   */
  private bool $buildedUp = false;


  /**
   *
   * @param string $templateDirectory Fully qualified path name
   * @param string $skeletonFile Name of skeleton file,
   * for example 'skeleton.html'
   *
   * @throws \Dominicus75\Templater\DirectoryNotFoundException
   * if given directory does not exists
   * @throws \Dominicus75\Templater\FileNotFoundException
   * if given skeleton file does not exists
   */
  public function __construct(string $templateDirectory, string $skeletonFile){

    if(is_dir($templateDirectory)) {
      $this->templateDirectory = $templateDirectory.DIRECTORY_SEPARATOR;
      try {
        parent::__construct($this->templateDirectory.$skeletonFile);
      } catch(FileNotFoundException $e) { throw $e; }
    } else {
      throw new DirectoryNotFoundException($templateDirectory.' does not exists.');
    }

    if(preg_match_all(Templater::MARKERS['template'], $this->source, $matches)) {
      foreach($matches[0] as $marker){ $this->templates[$marker] = null; }
    } else {
      throw new \InvalidArgumentException(
        'No template markers found in this skeleton file'
      );
    }

  }


  /**
   *
   * @parem string $marker in form '@@marker@@'
   * @param string $templateFile name of template (tpl) file, for example 'nav.tpl'
   * or 'page/read.tpl'
   * @throws \InvalidArgumentException if marker is not found
   * @throws \InvalidArgumentException if marker already exists
   *
   */
  public function assignTemplate(string $marker, string $templateFile): void {

    if(array_key_exists($marker, $this->templates)){
      try {
        $template = new Template($this->templateDirectory.$templateFile);
        $this->templates[$marker] = $template->getSource();
      } catch(FileNotFoundException $e) { throw $e; }
    } else {
      throw new \InvalidArgumentException($marker.' is not found in this skeleton file');
    }

  }


  /**
   *
   * @parem string $marker in form '@@marker@@'
   * @param string $renderedTemplate a rendered template
   * @throws \InvalidArgumentException if marker is not found
   * @throws \InvalidArgumentException if marker already exists
   *
   */
  public function assignRenderedTemplate(string $marker, string $renderedTemplate): void {

    if(array_key_exists($marker, $this->templates)){
      $this->templates[$marker] = $renderedTemplate;
    } else {
      throw new \InvalidArgumentException($marker.' is not found in this skeleton file');
    }

  }


  /**
   *
   * @param string $outerTemplateFile name of template (tpl) file, for example 'nav.tpl'
   * @param string $itemTemplateFile name of iterative template file (tpl) for example 'navItem.tpl'
   * @param string $marker in form '@@marker@@'
   * @param array $content
   *
   * @throws \Dominicus75\Templater\FileNotFoundException if the Looper or Item
   * template file does not exists
   * @throws \InvalidArgumentException if foreach marker is invalid or missing
   * @throws \InvalidArgumentException if either $content item is not an array
   *
   */
  public function assignTemplateLooper(
    string $outerTemplateFile,
    string $itemTemplateFile,
    string $marker,
    array $content
  ): void {

    if(array_key_exists($marker, $this->templates)){
      try {
        $looper = new TemplateIterator(
          $this->templateDirectory.$outerTemplateFile,
          $this->templateDirectory.$itemTemplateFile,
          str_replace('@@', '&&', $marker),
          $content
        );
        $this->templates[$marker] = $looper->render();
      } catch(\InvalidArgumentException |
              \RuntimeException |
              \FileNotFoundException $e) { $e->getMessage(); }
    } else {
      throw new \InvalidArgumentException($marker.' is not found in this skeleton file');
    }

  }


  /**
   * It change markers to template source in the skeleton
   *
   * @param void
   * @return void
   * @throws \RuntimeException, if any template or variable is missing
   *
   */
  public function buildLayout(): void {

    foreach($this->templates as $marker => $template){
      if(is_null($template)) { throw new \RuntimeException($marker.' template is missing'); }
      $this->source = str_replace($marker, $template, $this->source);
    }

    try {
      $this->extractVariableMarkers();
    } catch(\RuntimeException $e) { throw $e; }

    $this->buildedUp = true;

  }


  /**
   *
   * @param void
   * @return bool
   *
   */
  public function isRenderable(): bool {
    return ($this->buildedUp && $this->renderable) ? true : false;
  }


}
