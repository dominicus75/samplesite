<?php
/*
 *
 * @package Router
 * @author Domokos Endre János <domokos.endrejanos@gmail.com>
 * @copyright 2020 Domokos Endre János
 * @license GNU General Public License v3 (https://opensource.org/licenses/GPL-3.0)
 *
 *
 */

namespace Dominicus75\Router;

class Router
{

  const DEFAULT_CONTROLLER  = 'page';
  const DEFAULT_ACTION      = 'read';

  private $method;      //Request method
  private $controller;  //Controller name
  private $action;      //Controller method name
  private $uri;         //Content identifier

  private $routes = [
    'category' => '\Application\Controller\Category',
    'page'     => '\Application\Controller\Page',
    'error'    => '\Application\Controller\Error',
    'guestbook' => '\Application\Controller\Guestbook'
  ];


  /**
   *
   * @param string $requestTarget return of Dominicus75\Http\Request::getUri()
   * @return instance
   *
   */
  public function __construct(string $method, string $requestUri) {

    $updel  = "\/(?P<action>update|delete)";
    $create = "\/(?P<action>create)";
    $type   = "\/(?P<type>[a-zA-Z]{4,20})";
    $cid    = "\/(?P<cid>[a-zA-Z0-9_\-]{1,128}(\.html|\/))";

    if(preg_match("/^(\/?|index\.(php|html?))$/", $requestUri)) {

      $controller = self::DEFAULT_CONTROLLER;
      $uri        = 'index';

    } elseif(preg_match("/^".$updel.$type.$cid."$/i", $requestUri, $matches)) {

      $controller = $matches['type'];
      $action     = $matches['action'];
      $uri        = $matches['cid'];

    } elseif(preg_match("/^".$create.$type."$/i", $requestUri, $matches)) {

      $controller = $matches['type'];
      $action     = $matches['action'];

    } elseif(preg_match("/^".$type.$cid."$/i", $requestUri, $matches)) {

      $controller = $matches['type'];
      $uri        = $matches['cid'];

    } elseif(preg_match("/^".$cid."$/i", $requestUri, $matches)) {

      if(preg_match("/^(guestbook\.html)$/", $matches['cid'])) {
        $controller = 'guestbook';
      } else {
        $controller = preg_match("/\/$/", $matches['cid']) ? 'category' : 'page';
      }
      $uri = $matches['cid'];

    } else { throw new InvalidUriException(); }

    if(isset($controller) && !array_key_exists($controller, $this->routes)) {
      throw new InvalidUriException();
    }

    if(isset($uri)) {
      if(preg_match("/\/$/", $uri)) { $controller = 'category'; }
      $uri = preg_replace("/(\.html|\/)$/", "", $uri);
    }

    $this->method     = $method;
    $this->controller = isset($controller) ? $controller : self::DEFAULT_CONTROLLER;
    $this->action     = isset($action) ? $action : self::DEFAULT_ACTION;
    $this->uri        = isset($uri) ? $uri : null;

  }


  public function addRoute(string $type, string $controller) {
    $this->routes[$type] = $controller;
  }


  public function deleteRoute(string $type):void {
    unset($this->routes[$type]);
  }


  public function hasRoute(string $type):bool {
    return array_key_exists($type, $this->routes);
  }


  public function dispatch(string $method = null, string $action = null, string $uri = null):Object {
    if($this->hasRoute($this->controller)) {
      return new $this->routes[$this->controller]($this->method, $this->action, $this->uri);
    } else { throw new ControllerNotFoundException($this->controller); }
  }

}
