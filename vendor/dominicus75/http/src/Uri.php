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

class Uri
{


  public static function getScheme():string {
    return preg_match("/^(http|https)$/i", $_SERVER['REQUEST_SCHEME'])
            ? strtolower($_SERVER['REQUEST_SCHEME'])
            : strtolower(explode('/', $_SERVER['SERVER_PROTOCOL'])[0]);
  }


  public static function getHost(): string {
    return ($_SERVER['HTTP_HOST'] == $_SERVER['SERVER_NAME'])
            ? $_SERVER['HTTP_HOST']
            : $_SERVER['SERVER_NAME'] ;
  }


  public static function getPort(): int {
    return preg_match("/^(80|443)$/", $_SERVER['SERVER_PORT'])
            ? $_SERVER['SERVER_PORT'] : 80;
  }


  public static function getPath(): string {

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    return  preg_match("/^\/([\/a-zA-Z0-9_\-\.~]{1,128})?$/i", $path) ? $path : '/';

  }


  public static function getQuery():string {

    if(isset($_SERVER['QUERY_STRING'])) {
      return preg_match("/^([a-zA-Z0-9_\-\&\=\.\/]{10,128})$/i", $_SERVER['QUERY_STRING'])
             ? $_SERVER['QUERY_STRING'] : '';
    } else { return ''; }

  }

}
