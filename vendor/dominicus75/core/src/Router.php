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

class Router
{

  /**
   *
   * An array, what contains the roles, what belong to this site
   * @var array in form [0 => 'role0', 1 => 'role1', ... N => 'roleN']
   *
   */
  private array $roles;

  /**
   *
   * Associative array, what contains the controllers
   * @var array in form (string)'controllerName' => (string)'\Fully\Qualified\ClassName'
   *
   */
  private array $controllers;

  /**
   *
   * Associative array, what contains the allowed methods of roles
   * @var array ['role_name' => 'controllerName' => [0 => 'method0', 1 => 'method1', ... N => 'methodN']]
   *
   */
  private array $methods;

  /**
   *
   * Associative array, what contains the default values
   * @var array
   *
   */
  private array $defaults;

  /**
   *
   * @var Router\Route the current route
   *
   */
  private Router\Route $route;


  /**
   *
   * @param string $requestUri the requested URI
   * @param \ArrayAccess|null Config object
   * @see Router/config.php for defaults
   * @return self
   *
   */
  public function __construct(string $requestUri, ?\ArrayAccess $config = null){

    if(is_null($config) || (!($config instanceof \ArrayAccess) || !($config instanceof Config))) {
      $config = new Config('config', __DIR__.DIRECTORY_SEPARATOR.'Router');
    }

    $this->roles       = $config->offsetGet('roles');
    $this->controllers = $config->offsetGet('controllers');
    $this->methods     = $config->offsetGet('methods');
    $this->defaults    = $config->offsetGet('defaults');

    if(preg_match("/^\/?$/i", $requestUri)) {
      $this->route = new Router\Route(
        $this->defaults['role'],
        $this->controllers[$this->defaults['controller']],
        $this->defaults['method'],
        $this->defaults['content'],
        $this->defaults['category']
      );
      return $this;
    }

    $roles = "\/(?P<role>".implode('|', array_values($this->roles)).")";

    if(preg_match("/^".$roles."/i", $requestUri, $matches)) {
      $role    = $matches['role'];
      $pattern = "\/".$role;
      $actn    = "\/(?P<action>".implode('|', array_values($this->methods[$role][$role])).")(?P<extension>\.[a-z]{2,4})?";
      if(preg_match("/^".$pattern.$actn."$/i", $requestUri, $matches)) {
        $this->route = new Router\Route(
          $role,
          $this->controllers[$role],
          $matches['action'],
          null,
          null
        );
        return $this;
      } elseif(preg_match("/^".$pattern."\/[a-zA-Z0-9_-]+(\.[a-z]{2,4})?$/i", $requestUri, $matches)) {
        $this->route = new Router\Route(
          $this->defaults['role'],
          $this->controllers['message'],
          $this->defaults['method'],
          '403',
          $this->defaults['category']
        );
        return $this;
      }
    } else {
      $role    = $this->defaults['role'];
      $pattern = "";
    }

    $type = "\/(?P<controller>".implode('|', array_keys($this->methods[$role])).")";

    if(preg_match("/^".$pattern.$type."/i", $requestUri, $matches)) {
      $controller = $matches['controller'];
      $pattern   .= "\/".$controller;
      $actn = "\/(?P<action>".implode('|', array_values($this->methods[$role][$controller])).")(?P<extension>\.[a-z]{2,4})?";
      if(preg_match("/^".$pattern.$actn."/i", $requestUri, $matches)) {
        if(isset($matches['extension'])) {
          $this->route = new Router\Route(
            $role,
            $this->controllers[$controller],
            $matches['action'],
            null,
            null
          );
          return $this;
        } else {
          $action   = $matches['action'];
          $pattern .= "\/".$action;
        }
      } else {
        $this->route = new Router\Route(
          $this->defaults['role'],
          $this->controllers['message'],
          $this->defaults['method'],
          '403',
          $this->defaults['category']
        );
        return $this;
      }
    }

    $path = "\/(?P<slug>[a-zA-Z0-9_\-\/]{1,255}(?P<extension>\.[a-z]{2,4})?)";

    if(preg_match("/^".$pattern.$path."/i", $requestUri, $matches)) {

      $uri = explode('/', $matches['slug']);
      $ext = isset($matches['extension']) ? $matches['extension'] : '';

      if(count($uri) > 1){
        if(!isset($controller)) {
          $controller = preg_match("/\.html$/i", end($uri)) ? 'article' : 'category';
        }
        $slug       = array_pop($uri);
        $content    = str_replace($ext, "", $slug);
        $category   = implode('/', $uri);
      } else {
        if(!isset($controller)) {
          $controller = preg_match("/\.html$/i", $uri[0]) ? 'page' : 'category';
        }
        $content    = str_replace(".html", "", $uri[0]);
        $category   = null;
      }

      $action = isset($action) ? $action : $this->defaults['method'];

    }

    if($this->hasController($controller)) {
      $controller = $this->controllers[$controller];
      if($this->hasMethod($controller, $action)) {
        $method     = $action;
      } else {
        $controller = $this->controllers['message'];
        $method     = $this->defaults['method'];
        $content    = '404';
        $category   = $this->defaults['category'];
      }
      $content    = isset($content) ? $content : null;
      $category   = isset($category) ? $category : $this->defaults['category'];
    } else {
      $role       = $this->defaults['role'];
      $controller = $this->controllers['message'];
      $method     = $this->defaults['method'];
      $content    = '404';
      $category   = $this->defaults['category'];
    }

    $this->route = new Router\Route($role, $controller, $method, $content, $category);

  }


  /**
   * Checks whether a specific type has a controller
   *
   * @param string $type content type (e. g. 'article' or 'page')
   * @return bool
   *
   */
  public function hasController(string $type): bool {
    if(array_key_exists($type, $this->controllers)) {
      $controller = $this->controllers[$type];
      try {
        $reflection = new \ReflectionClass($controller);
        return true;
      } catch(\ReflectionException $e) { return false; }
    }
    return false;
  }


  /**
   * Checks whether a specific $method is defined in the given $type
   *
   * @param string $controller '\Fully\Qualified\ClassName'
   * @param string $method method name (e. g. 'view', 'create', 'login')
   * @return bool
   *
   */
  public function hasMethod(string $controller, string $method): bool {
    try {
      $reflection = new \ReflectionClass($controller);
      return $reflection->hasMethod($method);
    } catch(\ReflectionException $e) { return false; }
  }


  /**
   *
   * @param string $type content type (e. g. 'article' or 'page')
   * @param string $controller '\Fully\Qualified\ClassName'
   * @return self
   *
   */
  public function addController(string $type, string $controller): self {
    if(!$this->hasController($type)) {
      $this->controllers[$type] = $controller;
    }
    return $this;
  }


  /**
   *
   * @param string $type content type (e. g. 'article' or 'page')
   * @return self
   *
   */
  public function deleteController(string $type): self {
    if($this->hasController($type)) { unset($this->controllers[$type]); }
    return $this;
  }


  /**
   *
   * @param void
   * @return Router\Route a Route instance
   * @throws Router\RouteNotFoundException if Route not exists
   *
   */
  public function dispatch(): Router\Route {
    if(isset($this->route)) {
      return $this->route;
    } else { throw new Router\RouteNotFoundException(); }
  }

}
