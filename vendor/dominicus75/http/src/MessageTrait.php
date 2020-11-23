<?php
/*
 * @file MessageTrait.php
 * @package Http
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Http;

trait MessageTrait
{

  private array $headers;
  private $body;


  public function getProtocolVersion():string {
    return explode('/', $_SERVER['SERVER_PROTOCOL'])[1];
  }


  public function getHeaders():array {

    $result = [];

    foreach($this->headers as $name => $value) { $result[$name] = $this->getHeader($name); }

    return $result;

  }


  public function hasHeader(string $name):bool {
    return array_key_exists($name, $this->headers);
  }


  public function getHeader(string $name):array {

    $result = [];

    if($this->hasHeader($name)) {
      foreach(explode(',', $this->headers[$name]) as $value){ $result[$name][] = $value; }
    }

    return $result;

  }


  public function getBody() { return $this->body; }

}
