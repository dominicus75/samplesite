<?php
/*
 * @file FrontController.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application;

use \Dominicus75\Http\{Request, Response, Uri};
use \Dominicus75\Router\Router as Router;
use \Dominicus75\VariousTools\Config;

class FrontController
{


  public static function run() {

    $get   = (count($_GET) > 0) ? $_GET : null;
    $post  = (count($_POST) > 0) ? $_POST : null;
    $files = (count($_FILES) > 0) ? $_FILES : null;

    try{

      $request = new Request($get, $post, $files);
      $router  = new Router($request);
      $params  = $router->dispatch();
      $cName   = "\\Application\\Controller\\{$params['controller']}";
      $action  = $params['action'];
      $controller = new $cName($params['url'], $params['action'], $request);
      $controller->{$params['action']}();

    } catch(\Dominicus75\Router\InvalidUriException | \Dominicus75\Router\ControllerNotFoundException $e) {
      $response->redirect("/error/404.html");
    }

  }

}
