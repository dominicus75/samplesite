<?php
/*
 *
 * @package Core
 * @author Domokos Endre János <domokos.endrejanos@gmail.com>
 * @copyright 2020 Domokos Endre János
 * @license GNU General Public License v3 (https://opensource.org/licenses/GPL-3.0)
 *
 *
 */

namespace Dominicus75\Core;

use \Dominicus75\Http\{Request, Response};

class Router
{

  const DEFAULT_CONTROLLER  = 'page';
  const DEFAULT_ACTION      = 'read';
  const DEFAULT_CONTENT     = '/';

  /**
   *
   * Associative array of controllers
   * @var array
   *
   */
  private $controllers = [
    'album'    => '\Application\Controller\Album',
    'article'  => '\Application\Controller\Article',
    'category' => '\Application\Controller\Category',
    'fault'    => '\Application\Controller\Fault',
    'index'    => '\Application\Controller\Index',
    'page'     => '\Application\Controller\Page'
  ];

  /**
   *
   * @var Router\Route the current route
   *
   */
  private Router\Route $route;


  public function __construct(string $requestUri){

    $action = "\/(?P<action>edit|delete|create)";
    $edidel = "\/(?P<action>edit|delete)";
    $create = "\/(?P<action>create)";
    $type   = "\/(?P<type>".implode('|', array_keys($this->controllers)).")";
    $slug   = "\/(?P<slug>[a-zA-Z0-9_\-\/]{1,255}(\.html)?)";

    if(preg_match("/^\/?$/i", $requestUri)) {
      $this->route = new Router\Route(
        $this->controllers[self::DEFAULT_CONTROLLER],
        self::DEFAULT_ACTION,
        self::DEFAULT_CONTENT,
        null
      );
      return $this;
    } elseif(!preg_match("/^".$action."/i", $requestUri)) {
      $action = self::DEFAULT_ACTION;
      if(!preg_match("/^".$type."/i", $requestUri)) {
        $requestUri = ltrim($requestUri, '/');
        $path = explode('/', $requestUri);
        if(count($path) > 1){
          $controller = preg_match("/\.html$/i", end($path)) ? 'article' : 'category';
        } else {
          $controller = preg_match("/\.html$/i", $path[0]) ? 'page' : 'category';
        }
        $slug = $requestUri;
      } elseif(preg_match("/^".$type.$slug."$/i", $requestUri, $matches)) {
        $controller = $matches['type'];
        $slug       = $matches['slug'];
      } else {
        $controller = $this->controllers['fault'];
        $slug       = '404';
      }
    } elseif(preg_match("/^".$edidel.$type.$slug."$/i", $requestUri, $matches)) {
      $controller = $matches['type'];
      $action     = $matches['action'];
      $slug       = $matches['slug'];
    } elseif(preg_match("/^".$create.$type."$/i", $requestUri, $matches)) {
      $controller = $matches['type'];
      $action     = $matches['action'];
      $slug       = null;
    }

    if($this->hasController($controller)) {
      $controller = $this->controllers[$controller];
      try {
        $reflection = new \ReflectionClass($controller);
        if(!$reflection->hasMethod($action)) {
          $controller = $this->controllers['fault'];
          $action     = self::DEFAULT_ACTION;
          $slug       = '404';
        }
      } catch(\ReflectionException $e) {
        $controller = $this->controllers['fault'];
        $action     = self::DEFAULT_ACTION;
        $slug       = '404';
      }
    } else {
      $controller = $this->controllers['fault'];
      $action     = self::DEFAULT_ACTION;
      $slug       = '404';
    }

    $slug = str_replace(".html", "", $slug);
    $url  = explode('/', $slug);

    if(count($url) > 1) {
      $content  = array_pop($url);
      $category = implode('/', $url);
    } else {
      $content  = $slug;
      $category = null;
    }

    $this->route = new Router\Route($controller, $action, $content, $category);

  }


  public function addController(string $type, string $controller): self {
    if(!$this->hasController($type)) {
      $this->controllers[$type] = $controller;
      return $this;
    }
  }


  public function deleteController(string $type): self {
    if($this->hasController($type)) { unset($this->controllers[$type]); }
    return $this;
  }


  public function hasController(string $type): bool {
    return array_key_exists($type, $this->controllers);
  }


  public function dispatch(): Router\Route {
    if(isset($this->route)) {
      return $this->route;
    } else { throw new Router\RouteNotFoundException(); }
  }

}
