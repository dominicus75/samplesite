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

?>

<ul>
  <li><a href="/">/</a></li>
  <li><a href="/oldal.html">/oldal.html</a></li>
  <li><a href="/kategoria">/kategoria</a></li>
  <li><a href="/kategoria/alkategoria">/kategoria/alkategoria</a></li>
  <li><a href="/kategoria/oldal.html">/kategoria/oldal.html</a></li>
  <li><a href="/kategoria/alkategoria/oldal.html">/kategoria/alkategoria/oldal.html</a></li>
  <li><a href="/user/register.html">/user/register.html</a></li>
  <li><a href="/user/login.html">/user/login.html</a></li>
  <li><a href="/user/logout.html">/user/logout.html</a></li>
  <li><a href="/admin/login.html">/admin/login.html</a></li>
  <li><a href="/admin/logout.html">/admin/logout.html</a></li>
  <li><a href="/user/profile/view/gipsz-jakab.html">/user/profile/view/gipsz-jakab.html</a></li>
  <li><a href="/user/profile/edit/gipsz-jakab.html">/user/profile/edit/gipsz-jakab.html</a></li>
  <li><a href="/user/profile/delete/gipsz-jakab.html">/user/profile/delete/gipsz-jakab.html</a></li>
  <li><a href="/user/article/create.html">/user/article/create.html</a></li>
  <li><a href="/user/page/create.html">/user/page/create.html</a></li>
  <li><a href="/user/article/edit/valami-cikk.html">/user/article/edit/valami-cikk.html</a></li>
  <li><a href="/user/article/delete/valami-cikk.html">/user/article/delete/valami-cikk.html</a></li>
  <li><a href="/admin/article/create.html">/admin/article/create.html</a></li>
  <li><a href="/admin/article/edit/kategoria/alkategoria/valami-cikk.html">/admin/article/edit/kategoria/alkategoria/valami-cikk.html</a></li>
  <li><a href="/admin/article/delete/kategoria/alkategoria/valami-cikk.html">/admin/article/delete/kategoria/alkategoria/valami-cikk.html</a></li>
  <li><a href="/admin/article/view/kategoria/alkategoria/valami-cikk.html">/admin/article/view/kategoria/alkategoria/valami-cikk.html</a></li>
  <li><a href="/ajax/get/valami.json">/ajax/get/valami.json</a></li>
  <li><a href="/ajax/get/user/profile/gipsz-jakab.json">/ajax/get/user/profile/gipsz-jakab.json</a></li>
  <li><a href="/ajax/post/valami.json">/ajax/post/valami.json</a></li>
  <li><a href="/message/404.html">/message/404.html</a></li>
  <li><a href="/">/</a></li>
  <li><a href="/">/</a></li>
</ul>


<?php

/*$skeleton = new \Dominicus75\Templater\Skeleton(CSS);
$skeleton->insertHead('page');
$skeleton->assignComponent('%%header%%', TPL, 'header.tpl');
$model = new \Application\Model\Nav();
$skeleton->insertNav($model->getPages(), $model->getCategories(), TPL.'nav'.DSR);

//$nav = new \Dominicus75\Templater\Nav($model->getPages(), $model->getCategories(), TPL.'nav'.DSR);

//var_dump($skeleton->getSource());
echo $skeleton->getSource();*/

    $get   = (count($_GET) > 0) ? $_GET : null;
    $post  = (count($_POST) > 0) ? $_POST : null;
    $files = (count($_FILES) > 0) ? $_FILES : null;

    try{

      $request  = new \Dominicus75\Http\Request($get, $post, $files);
      $response = new \Dominicus75\Http\Response();
      $router   = new \Dominicus75\Core\Router($request->getUri());//, new \Dominicus75\Core\Config('router'));
      $route    = $router->dispatch();
echo "<pre>";
var_dump($route);
echo "</pre>";
      /*switch($route->controller) {
        case '\Application\Controller\Fault':
          $controller = new Controller\Fault($route->cid);
          $response->setStatusCode($route->cid);
          $responseBody = $controller->read();
        break;
        default:
          $controller   = new $route->controller($route, $request);
          $responseBody = $controller->{$route->action}();
          if(is_null($responseBody)) {
            $controller = new Controller\Fault(404);
            $response->setStatusCode(404);
            $responseBody = $controller->read();
          }
        break;
      }*/

    } catch(\Dominicus75\Core\Router\ControllerNotFoundException
            | \Dominicus75\Core\Router\MethodNotFoundException
            | \Dominicus75\Core\Router\RouteNotFoundException $e)
    {
      $controller = new Controller\Fault(404, ['message' => $e->getMessage()]);
      $response->setStatusCode(404);
      $responseBody = $controller->read();
    }

    //$response->setBody($responseBody);
    //$response->send();

  }

}
