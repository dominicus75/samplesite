<?php
/*
 * @file ArrayTemplate.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;

use \Dominicus75\Exceptions\{DirectoryNotFoundException, FileNotFoundException};


class ArrayTemplate
{

  public const VAR_MARKER = "/:{[a-zA-Z0-9_-]+}:/is";

  protected string $templateDir;
  protected string $itemTemplateFile;
  protected array $content;
  protected string $template;
  protected bool $renderable =  false;
  protected string $rendered = '';

  public function __construct(
    string $templateDir,
    string $itemTemplateFile,
    array $content
  ){

    if(is_dir($templateDir)) {
      $this->templateDir = $templateDir;
    } else {
      throw new DirectoryNotFoundException($templateDir.' is not a directory.');
    }

    if(is_file($this->templateDir.DIRECTORY_SEPARATOR.$itemTemplateFile)) {
      $this->itemTemplateFile = $itemTemplateFile;
      $this->template = file_get_contents($this->templateDir.DIRECTORY_SEPARATOR.$itemTemplateFile);
    } else { throw new FileNotFoundException($tplFile.' does not exists.'); }

    if(!empty($content)) {
      foreach($content as $id => $item) {
        if(!is_array($item)) {
          throw new \DomainException(
            'The elements of $content array must be arrays.'
          );
        }
        foreach($item as $key => $value) {
          $this->content[$id][':{'.$key.'}:'] = $value;
        }
      }
    } else { throw new \DomainException('$content array is very empty.'); }

    $this->renderable = true;

  }


  public function render(): string {

    if($this->renderable) {
      foreach($this->content as $item) {
        $this->rendered .= str_replace(array_keys($item), array_values($item), $this->template);
      }
      return $this->rendered;
    } else {
      throw new \RuntimeException('This template is not renderable yet');
    }

  }


}
