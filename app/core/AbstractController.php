<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Core;

use \Dominicus75\Http\Request;
use \Dominicus75\Templater\Skeleton;
use \Dominicus75\Config\{
  Config,
  NotFoundException,
  NotReadableException,
  NotWriteableException
};
use \Dominicus75\Router\Route;
use \Dominicus75\Model\AbstractModel;

abstract class AbstractController
{

  use ParameterizableTrait;

  /**
   *
   * @var Dominicus75\Router\Route current route instance
   *
   */
  protected Route $route;

  /**
   *
   * @var \Dominicus75\Model\AbstractModel current model object
   *
   */
  protected AbstractModel $model;

  /**
   *
   * @var AbstractView current view object
   *
   */
  protected AbstractView $view;

  /**
   *
   * @var \Dominicus75\Config\Config instance
   *
   */
  protected Config $config;


  /**
   *
   * @param Router\Route $route current route instance
   * @param array $parameters optional parameters of this controller
   *
   * @throws \Dominicus75\Config\NotFoundException
   * @throws \Dominicus75\Config\NotReadableException
   * @throws \Dominicus75\Config\NotWriteableException
   * @throws \PDOException
   *
   */
  protected function __construct(
    Router\Route $route,
    array $parameters = []
  ){

    $this->route = $route;
    $this->setParameters($parameters);
    $configFile  = $this->getParameter('config_file').'_controller';

    try {
      $this->config = new Config($configFile);
      $model = $this->hasParameter('model') ? $this->getParameter('model') : $this->config->offsetGet('model');
      $table = $this->hasParameter('table') ? $this->getParameter('table') : $this->config->offsetGet('table');
      $this->model  = new $model(new Config('mysql'), $table);
    } catch(\PDOException | NotFoundException | NotReadableException | NotWriteableException $e) { throw $e; }

  }

}
