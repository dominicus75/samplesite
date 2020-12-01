<?php
/*
 * @file Template.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;

use \Dominicus75\Exceptions\{DirectoryNotFoundException, FileNotFoundException};


class Template
{

  public const VAR_MARKER = "/:{[a-zA-Z0-9_-]+}:/is";

  protected string $templateDir;
  protected string $tplFile;
  protected array $content;
  protected string $template;
  protected bool $renderable =  false;

  public function __construct(
    string $templateDir,
    string $tplFile,
    array $content
  ){

    if(is_dir($templateDir)) {
      $this->templateDir = $templateDir;
    } else {
      throw new DirectoryNotFoundException($templateDir.' is not a directory.');
    }

    if(is_file($this->templateDir.DIRECTORY_SEPARATOR.$tplFile)) {
      $this->tplFile = $tplFile;
      $this->layout = file_get_contents($this->templateDir.DIRECTORY_SEPARATOR.$tplFile);
    } else { throw new FileNotFoundException($tplFile.' does not exists.'); }

    if(!empty($content)) {
      foreach($content as $key => $value) { $this->content[':{'.$key.'}:'] = $value; }
    } else { throw new \DomainException('$content array is very empty.'); }

    $this->renderable = true;

  }


  public function render(): string {

    if($this->renderable) {
      foreach($this->content as $marker => $content) {
        $this->layout = str_replace($marker, $content, $this->layout);
      }
      return $this->layout;
    } else {
      throw new \RuntimeException('This template is not renderable yet');
    }

  }


}
