<?php
/*
 * @file AbstractController.php
 * @package Core
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Core;

use \Dominicus75\Http\Request;
use \Dominicus75\Templater\Skeleton;

abstract class AbstractController
{

  use ParameterizableTrait;

  /**
   *
   * @var Dominicus75\Core\Router\Route current route instance
   *
   */
  protected Router\Route $route;

  /**
   *
   * @var \Dominicus75\Http\Request current request object
   *
   */
  protected Request $request;

  /**
   *
   * @var \Dominicus75\Core\Model\AbstractModel current model object
   *
   */
  protected Model\AbstractModel $model;

  /**
   *
   * @var \Dominicus75\Core\AbstractView current view object
   *
   */
  protected AbstractView $view;

  /**
   *
   * @var \Dominicus75\Core\Config instance
   *
   */
  protected Config $config;


  /**
   *
   * @param Router\Route $route current route instance
   * @param Request $request
   * @param Response $response
   *
   * @throws \Dominicus75\Core\Exceptions\DirectoryNotFoundException
   * @throws \Dominicus75\Core\Exceptions\FileNotFoundException
   * @throws \PDOException
   *
   */
  protected function __construct(
    Router\Route $route,
    Request $request,
    array $parameters = []
  ){

    $this->route     = $route;
    $this->request   = $request;
    $this->setParameters($parameters);

    $controller = explode('\\', $this->route->controller);
    $configFile = strtolower(end($controller)).'_controller';

    try {
      $this->config = new Config($configFile);
      $model = str_replace('Controller', 'Model', $this->route->controller);
      $table = $this->hasParameter('table') ? $this->getParameter('table') : $this->config->offsetGet('table');
      $this->model = new $model(new Config('mysql'), $table);
    } catch(\Throwable $e) { throw $e; }

  }

/*  public function read(): ?string {

    try {
      $content = $this->model->read(['category' => $this->route->category, 'cid' => $this->route->cid]);
      if(empty($content)) { return null; }
      $view = str_replace('Controller', 'View', $this->route->controller);
      $this->view = new $view($this->route->action, $content);
      return $this->view->render();
    } catch(\Throwable $e) { return null; }

  }*/

}
