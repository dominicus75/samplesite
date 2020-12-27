<?php
/*
 * @file AbstractController.php
 * @package Core
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Core;

use \Dominicus75\Http\{Request, Response};
use \Dominicus75\Templater\Skeleton;

abstract class AbstractController
{

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
   * @var \Dominicus75\Http\Response current response object
   *
   */
  protected Response $response;

  /**
   *
   * @var \Dominicus75\Core\Model\AbstractModel current model object
   *
   */
  protected Model\AbstractModel $model;

  /**
   *
   * @var Dominicus75\Core\AbstractView current view object
   *
   */
  protected AbstractView $view;

  /**
   *
   * @var array optional arguments
   *
   */
  protected array $arguments;

  /**
   *
   * @param Router\Route $route current route instance
   * @param Request $request
   * @param Response $response
   *
   */
  protected function __construct(
    Router\Route $route,
    Request $request,
    Response $response,
    array $arguments = []
  ){

    $this->route     = $route;
    $this->request   = $request;
    $this->response  = $response;
    $this->arguments = $arguments;

  }

  abstract public function create(): void;
  abstract public function read(): void;
  abstract public function edit(): void;
  abstract public function delete(): void;

}
