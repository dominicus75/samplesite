<?php
/*
 * @file FrontController.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application;

class FrontController
{


  public static function run() {

    $get   = (count($_GET) > 0) ? $_GET : null;
    $post  = (count($_POST) > 0) ? $_POST : null;
    $files = (count($_FILES) > 0) ? $_FILES : null;

    try{

      $request  = new \Dominicus75\Http\Request($get, $post, $files);
      $response = new \Dominicus75\Http\Response();
      $router   = new \Dominicus75\Core\Router($request->getUri());
      $route    = $router->dispatch();
      switch($route->controller) {
        case '\Application\Controller\Fault':
          $controller = new $route->controller($route->cid);
        break;
        default:
          $controller = new $route->controller($route, $request, $response);
          $controller->{$route->action}();
        break;
      }

    } catch(\Dominicus75\Core\Router\ControllerNotFoundException
            | \Dominicus75\Core\Router\MethodNotFoundException
            | \Dominicus75\Core\Router\RouteNotFoundException $e)
    {
      new Controller\Fault(404, $e->getMessage());
    }

  }

}
