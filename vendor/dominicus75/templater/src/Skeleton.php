<?php
/*
 * @file Skeleton.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Skeleton {

  /**
   *
   * @static string A regular expression to validate
   * the template file markers
   *
   */
  const MARKERS = [
    'template' => '/@@[a-zA-Z0-9_-]+@@/is',
    'variable' => '/{{[a-zA-Z0-9_-]+}}/is',
    'foreach'  => '/&&[a-zA-Z0-9_-]+&&/is'
  ];

  /**
   *
   * @var string Fully qualified path name of template directory
   *
   */
  private string $templateDirectory;

  /**
   *
   * @var string parsed content of the skeleton file
   *
   */
  private string $skeleton;

  /**
   *
   * @var array The templates belongs to this skeleton
   * in string @@marker@@ => Template $template form
   *
   */
  protected array $templates = [];

  /**
   *
   * @var array The string variables belongs to this skeleton
   * in string {{marker}} => string $variable form
   *
   */
  protected array $variables = [];

  /**
   *
   * @var bool if $this->skeleton includes all template sources, value of
   * this property true, false otherwise
   *
   */
  private bool $buildedUp = false;

  /**
   *
   * @var bool This Skeleton is rendereble or not
   *
   */
  protected bool $renderable = false;

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
    } else {
      throw new DirectoryNotFoundException($templateDirectory.' does not exists.');
    }

    if(is_file($this->templateDirectory.$skeletonFile)) {
      $this->skeleton = file_get_contents($this->templateDirectory.$skeletonFile);
    } else {
      throw new FileNotFoundException($skeletonFile.' does not exists.');
    }

    if(preg_match_all(self::MARKERS['template'], $this->skeleton, $matches)) {
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
        $this->templates[$marker] = $template->render();
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
        $looper = new TemplateLooper(
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
   * @throws \RuntimeException, if any template is missing
   *
   */
  public function buildLayout(): void {

    foreach($this->templates as $marker => $template){
      if(is_null($template)) { throw new \RuntimeException($marker.' template is missing'); }
      $this->skeleton = str_replace($marker, $template, $this->skeleton);
    }

    if(preg_match_all(self::MARKERS['variable'], $this->skeleton, $matches)) {
      foreach($matches[0] as $marker){ $this->variables[$marker] = null; }
    } else {
      throw new \RuntimeException(
        'No variable markers found in this skeleton file'
      );
    }

    $this->buildedUp = true;

  }


  /**
   *
   * @param string $marker in form '{{marker}}'
   * @param string $value
   * @throws \InvalidArgumentException if marker is not found
   * @throws \InvalidArgumentException if marker has already value
   *
   */
  private function bindValue(string $marker, string $value): void {

    if(array_key_exists($marker, $this->variables)){
      if(is_null($this->variables[$marker])) {
        $this->variables[$marker] = $value;
      } else {
        throw new \InvalidArgumentException($marker.' has already value');
      }
    } else {
      throw new \InvalidArgumentException($marker.' is not found in this template file');
    }

  }


  /**
   *
   * @param array $variables The string variables belongs to this Skeleton
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
   * @throws \RuntimeException if this skeleton or any template is not renderable
   *
   */
  public function render(): string {

    if($this->buildedUp){

      if(!$this->renderable){
        throw new \RuntimeException('This skeleton is not renderable yet.');
      }

      return str_replace(
              array_keys($this->variables),
              array_values($this->variables),
              $this->skeleton
             );

    } else {
      throw new \RuntimeException('This skeleton is not renderable yet.');
    }

  }


}
