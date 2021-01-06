<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application;

class Samplesite
{

  private \Dominicus75\Http\Request $request;
  private \Dominicus75\Http\Response $response;
  private \Dominicus75\Router\Route $route;
  private Object $controller;

  public function __construct() {

    $get   = (count($_GET) > 0) ? $_GET : null;
    $post  = (count($_POST) > 0) ? $_POST : null;
    $files = (count($_FILES) > 0) ? $_FILES : null;

    try{

      $this->request  = new \Dominicus75\Http\Request($get, $post, $files);
      $this->response = new \Dominicus75\Http\Response();
      $router         = new \Dominicus75\Router\Router($this->request->getUri(), new \Dominicus75\Config\Config('router'));
      $this->route    = $router->dispatch();
      unset($router);
//echo "<pre>";
//var_dump($this);
//var_dump(new \Dominicus75\Router\Mapper(new \Dominicus75\Config\Config('mapper')));
//echo "</pre>";

      /*switch($this->route->controller) {
        case '\Application\Controller\Message':
          $this->controller = new Controller\Message($this->route->cid);
          $this->response->setStatusCode($this->route->cid);
          $responseBody = $this->controller->view();
        break;
        default:
          $this->controller = new $this->route->controller($this->route, $this->request);
          $responseBody = $this->controller->{$this->route->action}();
          if(is_null($responseBody)) {
            $this->controller = new Controller\Message('404');
            $this->response->setStatusCode(404);
            $responseBody = $this->controller->view();
          }
        break;
      }*/

    } catch(
      \Dominicus75\Http\InvalidArgumentException |
      \Dominicus75\Http\RuntimeExceptionR $e
    ) {
      //$this->controller = new Controller\Message('Hiba', '/images/failure.jpg', $e->getMessage());
      //$responseBody = $this->controller->view();
    } catch(
      \Dominicus75\Core\Router\ControllerNotFoundException |
      \Dominicus75\Core\Router\MethodNotFoundException |
      \Dominicus75\Core\Router\RouteNotFoundException $e
    ) {
      //$this->controller = new Controller\Message('404');
      //$this->response->setStatusCode(404);
      //$responseBody = $this->controller->view();
    }

  }


  public function run() {


$skeleton = new \Dominicus75\Templater\Skeleton(CSS);
//$skeleton->insertHead('page');
//$skeleton->assignComponent('%%header%%', TPL, 'header.tpl');
//$model = new \Application\Model\Nav();
//$skeleton->insertNav(['pages' => $model->getPages(), 'categories' => $model->getCategories()], TPL.'nav'.DSR);*/

//$nav = new \Dominicus75\Templater\Nav([$model->getPages(), $model->getCategories()], TPL.'nav'.DSR);

//echo $skeleton->getSource();
echo "<pre>";
var_dump($skeleton);
//var_dump(new \Dominicus75\Router\Mapper(new \Dominicus75\Config\Config('mapper')));
echo "</pre>";

    //$this->response->setBody($responseBody);
    //$this->response->send();


  }

}
