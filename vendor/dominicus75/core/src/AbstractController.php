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
   * @param array $parameters optional parameters of this controller
   *
   * @throws \Dominicus75\Core\Exceptions\DirectoryNotFoundException
   * @throws \Dominicus75\Core\Exceptions\FileNotFoundException
   * @throws \PDOException
   *
   */
  protected function __construct(
    Router\Route $route,
    array $parameters = []
  ){

    $this->route     = $route;
    $this->setParameters($parameters);

    $controller = explode('\\', $this->route->controller);
    $configFile = strtolower(end($controller)).'_controller';

    try {
      $this->config = new Config($configFile);
      $model = $this->hasParameter('model') ? $this->getParameter('model') : $this->config->offsetGet('model');
      $table = $this->hasParameter('table') ? $this->getParameter('table') : $this->config->offsetGet('table');
      $this->model  = new $model(new Config('mysql'), $table);
    } catch(\Throwable $e) { throw $e; }

  }

}
