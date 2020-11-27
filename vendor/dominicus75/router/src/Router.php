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

use \Dominicus75\Http\{Request, Response};

class Router
{

  const DEFAULT_CONTROLLER  = 'page';
  const DEFAULT_ACTION      = 'read';

  private string $controller;  //Controller name
  private string $action;      //Controller method name
  private string $content;     //Content identifier

  private $routes = [
    'category'  => '\Application\Controller\Category',
    'page'      => '\Application\Controller\Page',
    'error'     => '\Application\Controller\Error',
    'guestbook' => '\Application\Controller\Guestbook'
  ];

  private Request $request;


  public function __construct(Request $request) {

    $this->request  = $request;

    $updel  = "\/(?P<action>update|delete)";
    $create = "\/(?P<action>create)";
    $type   = "\/(?P<type>[a-zA-Z]{4,20})";
    $cid    = "\/(?P<cid>[a-zA-Z0-9_\-]{1,128}(\.html|\/))";

    $requestUri = $this->request->getUri();

    if(preg_match("/^(\/?|index\.(php|html?))$/", $requestUri)) {

      $controller = self::DEFAULT_CONTROLLER;
      $content    = 'index';

    } elseif(preg_match("/^".$updel.$type.$cid."$/i", $requestUri, $matches)) {

      $controller = $matches['type'];
      $action     = $matches['action'];
      $content    = $matches['cid'];

    } elseif(preg_match("/^".$create.$type."$/i", $requestUri, $matches)) {

      $controller = $matches['type'];
      $action     = $matches['action'];

    } elseif(preg_match("/^".$type.$cid."$/i", $requestUri, $matches)) {

      $controller = $matches['type'];
      $content    = $matches['cid'];

    } elseif(preg_match("/^".$cid."$/i", $requestUri, $matches)) {

      if(preg_match("/^(guestbook\.html)$/", $matches['cid'])) {
        $controller = 'guestbook';
      } else {
        $controller = preg_match("/\/$/", $matches['cid']) ? 'category' : 'page';
      }
      $content = $matches['cid'];

    } else { throw new InvalidUriException(); }

    if(isset($controller) && !array_key_exists($controller, $this->routes)) {
      throw new InvalidUriException();
    }

    if(isset($content)) {
      if(preg_match("/\/$/", $content)) { $controller = 'category'; }
      $content = preg_replace("/(\.html|\/)$/", "", $content);
    }

    $this->controller = isset($controller) ? $controller : self::DEFAULT_CONTROLLER;
    $this->action     = isset($action) ? $action : self::DEFAULT_ACTION;
    $this->content    = isset($content) ? $content : null;

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


  public function dispatch(): \Dominicus75\MVC\ControllerInterface {
    if($this->hasRoute($this->controller)) {
      return new $this->routes[$this->controller](
                   $this->request,
                   $this->action,
                   $this->content
                 );
    } else { throw new ControllerNotFoundException($this->controller); }
  }

}
