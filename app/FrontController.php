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

class FrontController
{


  public static function run() {

    $get   = (count($_GET) > 0) ? $_GET : null;
    $post  = (count($_POST) > 0) ? $_POST : null;
    $files = (count($_FILES) > 0) ? $_FILES : null;

    try{

      $request  = new Request($get, $post, $files);
      $response = new Response();
      $router   = new Router($request);
      $controller = $router->dispatch();

      echo "<pre>";
      //var_dump($layout);
      //echo "<br><br>\n";
      var_dump($controller);
      //echo "<br><br>\n";
      echo "</pre>";


    } catch(\Dominicus75\Router\InvalidUriException | \Dominicus75\Router\ControllerNotFoundException $e) {
      $response->redirect("/error/404.html");
    }

  }

}
