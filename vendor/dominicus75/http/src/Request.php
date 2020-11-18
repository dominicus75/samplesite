<?php
/*
 *
 * @package Http
 * @author Domokos Endre János <domokos.endrejanos@gmail.com>
 * @copyright 2020 Domokos Endre János
 * @license GNU General Public License v3 (https://opensource.org/licenses/GPL-3.0)
 *
 *
 */

namespace Dominicus75\Http;


class Request
{


  protected string $method;
  protected string $uri;
  protected array $headers;

  public function __construct() {

    if(Uri::isValid()) {

      $this->method = preg_match("/^(get|post)$/i", $_SERVER['REQUEST_METHOD'])
                      ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET' ;
      $this->uri    = Uri::getPath();

      foreach(apache_request_headers() as $name => $value) {

        $this->headers[$name] = preg_match("/^([a-zA-Z0-9 _\-\.~:\/\?\#\[\]\@\!\$\&\'\(\)\*\+\,\;\=]{1,128})$/", $value)
                                ? $value : '' ;

      }

    } else { throw new InvalidUriException(); }

  }


  public function getMethod():string {
    return $this->method;
  }

  public function getUri():string {
    return $this->uri;
  }


  public function getHeaders():array {
    return $this->headers;
  }


  public function hasHeader(string $name):bool {
    return array_key_exists($name, $this->headers);
  }


  public function getHeader(string $name):?string {
    return ($this->hasHeader($name)) ? $this->headers[$name] : null;
  }


}
