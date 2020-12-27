<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Http\{Request, Response};
use \Dominicus75\Core\{
  AbstractController,
  Config as Config,
  Router\Route as Route,
  Model\ContentNotFoundException as ContentNotFoundException,
  Model\InvalidFieldNameException as InvalidFieldNameException,
  Model\InvalidStatementException as InvalidStatementException
};

class Category extends AbstractController
{

  public function __construct(
    Route $route,
    Request $request,
    Response $response
  ){

    parent::__construct($route, $request, $response);

    try {
      $this->model = new \Application\Model\Category(new Config('mysql'), 'categories');
    } catch(\PDOException | InvalidFieldNameException $e) {
      $e->getMessage();//$this->response->redirect('/fault/404.html');
    }

  }


  public function create(): void {




  }


  public function read(): void {

    try {
      $content = $this->model->read(['category' => $this->route->category, 'cid' => $this->route->cid]);
      $this->view = new \Application\View\Page($content);
      $this->response->setBody($this->view->render());
      $this->response->send();
    } catch(ContentNotFoundException $e) {
      $this->response->redirect('/fault/404.html');
    }

  }


  public function edit(): void {


  }


  public function delete(): void {


  }


}
