<?php
/*
 * @file Page.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Router\Route;
use \Dominicus75\Http\Request;
use \Dominicus75\Model\{
  Entity,
  ContentNotFoundException,
  InvalidFieldNameException,
  InvalidStatementException
};

class Page extends \Application\Core\AbstractController
{

  /**
   *
   * @param Router\Route $route current route instance
   * @param array $content (optional)
   *
   * @throws \Dominicus75\Config\NotFoundException
   * @throws \Dominicus75\Config\NotReadableException
   * @throws \Dominicus75\Config\NotWriteableException
   * @throws \PDOException
   *
   */
  public function __construct(
    Route $route,
    Request $request
  ){
    $parameters['content_type']  = 'page';
    $parameters['content_table'] = 'pages';
    parent::__construct($route, $parameters, $request);
  }


  public function create(): void {

  }

  public function view(): void {
    $variables = $this->model->selectData(
      ['url', $this->route->cid],
      ['url', 'title', 'description', 'image', 'body'],
      []
    );
    if(!empty($variables)) {
      $variables['image'] = '/upload/images/'.$variables['image'];
      $parameters = ['type' => 'page', 'action' => 'view'];
      if($this->route->role == 'admin') {
        $this->layout = new \Application\View\Admin\View($parameters);
      } else {
        $this->layout = new \Application\View\View($parameters);
      }
      $this->layout->updateVariables($variables);
      $this->success = true;
    } else { $this->redirect = '/message/404.html'; }
  }

  public function edit(): void {

  }

  public function delete(): void {}

}
