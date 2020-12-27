<?php
/*
 * @file Article.php
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

class Article extends AbstractController
{

  public function __construct(
    Route $route,
    Request $request,
    Response $response
  ){

    parent::__construct($route, $request, $response);

    try {
      $this->model = new \Application\Model\Article(new Config('mysql'), 'contents');
    } catch(\PDOException | InvalidFieldNameException $e) {
      new Fault(500, $e->getMessage());
    }

  }


  public function create(): void {




  }


  public function read(): void {

    try {
      $content = $this->model->read(['category' => $this->route->category, 'cid' => $this->route->cid]);
      $this->view = new \Application\View\Article($content, $this->route->action);
      $this->response->setBody($this->view->render());
      $this->response->send();
    } catch(InvalidFieldNameException $e) {
      new Fault(500, $e->getMessage());
    }

  }


  public function edit(): void {


  }


  public function delete(): void {


  }


}
