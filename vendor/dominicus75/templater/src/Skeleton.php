<?php
/*
 * @file Skeleton.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;

use \Dominicus75\Exceptions\{DirectoryNotFoundException, FileNotFoundException};

class Skeleton
{

  protected const TPL_MARKER = "/@@[a-zA-Z0-9]+\.tpl@@/is";

  protected string $templateDir;
  protected string $skeletonFile;
  protected array $templates;
  protected string $skeleton;
  protected bool $renderable =  false;

  public function __construct(
    string $templateDir,
    string $skeletonFile,
    array $templates
  ){

    if(is_dir($templateDir)) {
      $this->templateDir = $templateDir;
    } else {
      throw new DirectoryNotFoundException($templateDir.' is not a directory.');
    }

    if(is_file($this->templateDir.DIRECTORY_SEPARATOR.$skeletonFile)) {
      $this->skeletonFile = $skeletonFile;
      $this->skeleton = file_get_contents($this->templateDir.DIRECTORY_SEPARATOR.$this->skeletonFile);
    } else { throw new FileNotFoundException($skeletonFile.' does not exists.'); }

    if(!empty($templates)) {

      foreach($templates as $marker => $template) {

        if(!preg_match(static::TPL_MARKER, $marker)) {
          throw new \InvalidArgumentException($marker.' is invalid');
        }

        if(is_string($template)) {
          $this->templates[$marker] = $template;
        } else {
          throw new \InvalidArgumentException('The type of $template variable must be string.');
        }

      }

    } else { throw new \InvalidArgumentException('$templates array is very empty.'); }

    $this->renderable = true;

  }


  public function render(): string {

    if($this->renderable){
      foreach($this->templates as $marker => $template) {
        $this->skeleton = str_replace($marker, $template, $this->skeleton);
      }
      return $this->skeleton;
    } else {
      throw new \RuntimeException('This skeleton is not renderable yet.');
    }

  }

}