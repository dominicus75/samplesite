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
   * Associative array, what contains all of methods what controllers have
   * @var array ['controllerName' => [0 => 'method0', 1 => 'method1', ... N => 'methodN']]
   *
   */
  private array $methods;

  /**
   *
   * Associative array, what contains the enabled methods of roles
   * @var array ['role_name' => 'controllerName' => [0 => 'method0', 1 => 'method1', ... N => 'methodN']]
   *
   */
  private array $enabled;

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
    $this->enabled     = $config->offsetGet('enabled');
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
    $type  = "\/(?P<controller>".implode('|', array_keys($this->controllers)).")";
    $path  = "\/(?P<slug>[a-zA-Z0-9_\-\/]{1,255}(?P<extension>\.[a-z]{2,4})?)$";

    if(preg_match("/^".$roles."/i", $requestUri, $matches)) {
      $role       = $matches['role'];
      $requestUri = str_replace('/'.$role, '', $requestUri);
      $actn       = "\/(?P<action>[^\/.]{1,32})(?P<extension>\.[a-z]{2,4})?";
      if(preg_match("/^".$actn."$/i", $requestUri, $matches)) {
        $controller = $role;
        $action     = $matches['action'];
        $ext        = isset($matches['extension']) ? $matches['extension'] : '';
        $requestUri = str_replace('/'.$action.$ext, '', $requestUri);
      }
    } else { $role  = $this->defaults['role']; }

    if(!empty($requestUri) && preg_match("/^".$type."/i", $requestUri, $matches)) {
      $controller = $matches['controller'];
      $requestUri = str_replace('/'.$controller, '', $requestUri);
      $actn       = "\/(?P<action>".implode('|', array_values($this->methods[$controller])).")(?P<extension>\.[a-z]{2,4})?";
      if(preg_match("/^".$actn."/i", $requestUri, $matches)) {
        $action   = $matches['action'];
        $ext      = isset($matches['extension']) ? $matches['extension'] : '';
        if(isset($matches['extension'])) {
          $category = null;
          $content  = null;
        }
        $requestUri = str_replace('/'.$action.$ext, '', $requestUri);
      }
    }

    if(!empty($requestUri) && preg_match("/^".$path."/i", $requestUri, $matches)) {
      $uri = explode('/', $matches['slug']);
      $ext = isset($matches['extension']) ? $matches['extension'] : '';
      if(count($uri) > 1){
        if(!isset($controller)) {
          $controller = preg_match("/\.html$/i", end($uri)) ? 'article' : 'category';
        }
        $slug     = array_pop($uri);
        $content  = str_replace($ext, "", $slug);
        $category = implode('/', $uri);
      } else {
        if(!isset($controller)) {
          $controller = preg_match("/\.html$/i", $uri[0]) ? 'page' : 'category';
        }
        $content  = str_replace(".html", "", $uri[0]);
        $category = null;
      }
    }

    $action = isset($action) ? $action : $this->defaults['method'];

    if($this->hasController($controller)) {
      if(array_key_exists($controller, $this->enabled[$role])) {
        if($this->hasMethod($this->controllers[$controller], $action)) {
          if(in_array($action, $this->enabled[$role][$controller])) {
            $controller = $this->controllers[$controller];
            $method = $action;
          } else {
            $role       = $this->defaults['role'];
            $controller = $this->controllers['message'];
            $method     = $this->defaults['method'];
            $content    = '403';
            $category   = $this->defaults['category'];
          }
        } else {
          $role       = $this->defaults['role'];
          $controller = $this->controllers['message'];
          $method     = $this->defaults['method'];
          $content    = '404';
          $category   = $this->defaults['category'];
        }
      } else {
        $role       = $this->defaults['role'];
        $controller = $this->controllers['message'];
        $method     = $this->defaults['method'];
        $content    = '403';
        $category   = $this->defaults['category'];
      }
    } else {
      $role       = $this->defaults['role'];
      $controller = $this->controllers['message'];
      $method     = $this->defaults['method'];
      $content    = '404';
      $category   = $this->defaults['category'];
    }

    $content  = isset($content) ? $content : null;
    $category = isset($category) ? $category : $this->defaults['category'];

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
