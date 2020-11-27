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

  private $headers = [];


  public function getProtocolVersion():string {
    return explode('/', $_SERVER['SERVER_PROTOCOL'])[1];
  }


  private function setHeaders(array $headers):void {

    foreach($headers as $key => $value){
      if(!$this->addHeader($key, $value)){
        throw new \InvalidArgumentException("The $key header is already exists.");
      }
    }

  }


  private function sanitizeHeaderName(string $name):string {
    return trim(preg_replace("/^[^\w_\-]*$/i", "", $name));
  }


  private function sanitizeHeaderValue(string $value):string {
    return trim(preg_replace("/^[^\w\s\-\?\!\:\.\,\;\/\"\'\^\*\+=@&\{\}\(\)\[\]]*$/i", "", $value));
  }


  public function getHeaders(): array { return $this->headers; }


  public function hasHeader(string $name): bool {
    return array_key_exists($name, $this->headers);
  }


  public function addHeader(string $name, string $value): bool {

    if($this->hasHeader($name)) { return false; }

    $name  = $this->sanitizeHeaderName($name);
    $value = $this->sanitizeHeaderValue($value);
    $this->headers[$name] = $value;

    return true;

  }


  public function getHeader(string $name): ?string {
    return $this->hasHeader($name) ? $this->headers[$name] : null;
  }


}
