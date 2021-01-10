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

      $this->request    = new \Dominicus75\Http\Request($get, $post, $files);
      $this->response   = new \Dominicus75\Http\Response();
      $router           = new \Dominicus75\Router\Router($this->request->getUri(), new \Dominicus75\Config\Config('router'));
      $this->route      = $router->dispatch();
      unset($router);
      $this->auth       = new Core\Authority($this->route->role);

      if($this->route->role != 'visitor' && $this->route->method != 'login' && !$this->auth->authenticate()) {
        $this->response->redirect('/admin/login.html');
      }

      switch($this->route->controller) {
        case '\Application\Controller\Message':
          $this->controller = new Controller\Message($this->route);
          $this->response->setStatusCode($this->route->cid);
        break;
        /*case '\Application\Controller\Admin':
          if($this->auth->authenticate()) {
            //$this->response->redirect('/admin/dashboard.html');
          }
        break;*/
        default:
          $this->controller = new $this->route->controller($this->route, $this->request);
          $this->controller->{$this->route->method}();
        break;
      }


    } catch(
      \Dominicus75\Http\InvalidArgumentException |
      \Dominicus75\Http\RuntimeExceptionR $e
    ) {
      //$this->controller = new Controller\Message('Hiba', '/images/failure.jpg', $e->getMessage());
      //$responseBody = $this->controller->view();
    } catch(
      \Dominicus75\Router\ControllerNotFoundException |
      \Dominicus75\Router\MethodNotFoundException |
      \Dominicus75\Router\RouteNotFoundException $e
    ) {
      //$this->controller = new Controller\Message('404');
      //$this->response->setStatusCode(404);
      //$responseBody = $this->controller->view();
    }

  }


  public function run() {



//$page = new Controller\Page($this->route, $this->request);
//echo $page->display();
//echo $nav->getNav();

//$mnb = new Controller\MNB();
//$aside = new View\Aside(['@@mnb@@' => $mnb->display()]);
//echo $mnb->display();


//echo $skeleton->getSource();
//echo "<pre>";
//var_dump($admin);
//preg_match("/^(=|>|<|<>|<=|>=)$/i", "<>", $matches);
//var_dump($matches);
//var_dump(new \Dominicus75\Router\Mapper(new \Dominicus75\Config\Config('mapper')));
//echo "</pre>";



    $this->response->setBody($this->controller->display());
    $this->response->send();

  }

}
