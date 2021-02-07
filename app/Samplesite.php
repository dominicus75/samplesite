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
  private Core\Authority $auth;
  private Core\AbstractController $controller;

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

      if(!is_file(APP.'config'.DSR.'mysql.php') && $this->route->controller != '\Application\Controller\Install') {
        $this->response = new \Dominicus75\Http\Response();
        $this->response->redirect('/admin/install/start.html');
      }

      Core\Session::init();
      $this->auth     = new Core\Authority($this->route);

      switch($this->route->controller) {
        case '\Application\Controller\Message':
          $this->controller = new Controller\Message($this->route);
          $this->response->setStatusCode($this->route->cid);
        break;
        default:
          $this->controller = new $this->route->controller($this->route, $this->request);
        break;
      }

    } catch(
      \Dominicus75\Http\InvalidArgumentException |
      \Dominicus75\Http\RuntimeExceptionR $e
    ) {
      $this->controller = new Controller\Message(
        $this->route, [
          'title'       => 'Hiba',
          'description' => $e->getMessage(),
          'image'       => '/images/failure.jpg'
        ]
      );
      $this->response->setStatusCode(500);
      $this->response->send();
    } catch(
      \Dominicus75\Router\ControllerNotFoundException |
      \Dominicus75\Router\MethodNotFoundException |
      \Dominicus75\Router\RouteNotFoundException $e
    ) {
      $this->response->redirect('/message/404.html');
    }

  }


  public function run() {

    if(!$this->auth->authenticate() && !preg_match("/^(confirm|login|register|install)$/", $this->route->method)) {
      $this->response->redirect('/'.$this->route->role.'/login.html');
    }

    if(!$this->controller->hasRedirect()) {
      $this->response->setBody($this->controller->display());
      $this->response->send();
    } else {
      $this->response->redirect($this->controller->getRedirect());
    }

  }

}
