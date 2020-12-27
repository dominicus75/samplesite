<?php
/*
 * @file Source.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Source
{

  /**
   *
   * @var string source of the template file
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
      $this->source = file_get_contents($url);
    } else { throw new FileNotFoundException($url.' does not exists.'); }

  }

  /**
   *
   * @param void
   * @return string
   *
   */
  public function getSource(): string { return $this->source; }


}