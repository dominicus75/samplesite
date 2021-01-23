<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Core;

use \Dominicus75\Config\{
  Config,
  NotFoundException,
  NotReadableException,
  NotWriteableException
};
use \Dominicus75\Router\Route;
use \Dominicus75\Http\Request;
use \Dominicus75\Model\{Entity, EntityInterface};

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
   * @var \Dominicus75\Http\Request current request instance
   *
   */
  protected ?Request $request;

  /**
   *
   * @var \Dominicus75\Model\EntityInterface current model object
   *
   */
  protected EntityInterface $model;

  /**
   *
   * @var \Dominicus75\Templater\RenderableSource current view object
   *
   */
  protected \Dominicus75\Templater\RenderableSource $layout;

  /**
   *
   * @var array parameters
   *
   */
  protected array $parameters = [];

  /**
   *
   * @var bool running of call_user_func() method was a success or not
   *
   */
  protected bool $success = false;

  /**
   *
   * @var string redirect uri or null
   *
   */
  protected ?string $redirect = null;

  /**
   *
   * @param Router\Route $route current route instance
   * @param array $parameters parameters of this controller
   *
   * @throws \Dominicus75\Config\NotFoundException
   * @throws \Dominicus75\Config\NotReadableException
   * @throws \Dominicus75\Config\NotWriteableException
   * @throws \PDOException
   *
   */
  protected function __construct(Route $route, array $parameters, ?Request $request = null){

    try {
      $this->route      = $route;
      $this->request    = $request;
      $this->setParameters($parameters);
      if($this->hasParameter('content_type') && $this->hasParameter('content_table')) {
        $this->model    = new Entity(
          $this->parameters['content_type'],
          $this->parameters['content_table'],
          new Config('mysql')
        );
      }
      call_user_func(array($this, $this->route->method));
    } catch(\PDOException | NotFoundException | NotReadableException | NotWriteableException $e) {
      echo '<h2>'.$e->getMessage().'</h2>';
    }

  }


  /**
   *
   * @param void
   * @return string the rendered source
   * @throws \Dominicus75\Templater\Exceptions\NotRenderableException
   * if this Layout is not renderable yet
   *
   */
  public function display(): string {
    try {
      return $this->layout->display();
    } catch(\Dominicus75\Templater\Exceptions\NotRenderableException $e) {
      echo '<h2>'.$e->getMessage().'</h2>';
    }
  }


  /**
   *
   * Checks, that running of call_user_func() method was a success or not
   * @param void
   * @return bool
   *
   */
  public function isSuccess(): bool { return $this->success; }


  /**
   *
   * @param void
   * @return bool
   *
   */
  public function hasRedirect(): bool { return !is_null($this->redirect); }


  /**
   *
   * @param void
   * @return string|null
   *
   */
  public function getRedirect(): ?string { return $this->redirect; }

}
