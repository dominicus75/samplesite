<?php
/*
 * @file Template.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Template
{

  /**
   *
   *  @var string Fully qualified path name
   *
   */
  protected string $url;

  /**
   *
   * @var string parsed content of the template file
   *
   */
  protected string $source;

  /**
   *
   * @param string $url Fully qualified path name of template file (tpl|html)
   *
   * @throws \Dominicus75\Templater\FileNotFoundException if the template
   * file does not exists
   *
   */
  public function __construct(string $url) {

    if(is_file($url)) {
      $this->url = $url;
      $this->source = file_get_contents($this->url);
    } else { throw new FileNotFoundException($url.' does not exists.'); }

  }

  public function render(): string { return $this->source; }


}