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

use \Dominicus75\Validator\{Input, Pattern};


class Request
{

  use MessageTrait;

  protected string $uri;

  public function __construct() {

    if(Uri::isValid()) {

      $this->uri = Uri::getPath();

      foreach(apache_request_headers() as $name => $value) {

        $this->headers[$name] = preg_match("/^([a-zA-Z0-9 _\-\.~:\/\?\#\[\]\@\!\$\&\'\(\)\*\+\,\;\=]{1,128})$/", $value)
                                ? $value : '' ;

      }

      if($post = file_get_contents('php://input')) {

        foreach(explode("&", $post) as $item) {
          $array = explode("=", urldecode($item));
          $key   = Input::sanitizeHtml($array[0], null);
          $value = Input::sanitizeHtml($array[1], Pattern::ALLOWED_TAGS);
          $this->body[$key] = $value;
        }

      }

    } else { throw new InvalidUriException(); }

  }


  public function getMethod():string {
    return preg_match("/^(get|post)$/i", $_SERVER['REQUEST_METHOD'])
                      ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET' ;
  }

  public function getUri():string {
    return $this->uri;
  }


}
