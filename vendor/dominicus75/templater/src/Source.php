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
   * @param string $tplFile Fully qualified path name of template file (tpl|html)
   *
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException if the template
   * file does not exists
   *
   */
  public function __construct(string $tplFile = '') {
    if(!empty($tplFile)) {
      if(is_file($tplFile)) {
        $this->source = file_get_contents($tplFile);
      } else { throw new Exceptions\FileNotFoundException($tplFile.' does not exists.'); }
    } else { $this->source = ''; }
  }


  /**
   *
   * @param string $marker in form '%%marker%%' or '{{marker}}' or '@@marker@@'
   * what we are looking for
   * @return bool true, if marker was found in $this->source, false otherwise
   *
   */
  protected function hasMarker(string $marker): bool {
    return (bool)preg_match("/".$marker."/i", $this->source);
  }


  /**
   *
   * @param void
   * @return string
   *
   */
  public function getSource(): string { return $this->source; }


}