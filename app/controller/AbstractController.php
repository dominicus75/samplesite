<?php
/*
 * @file AbstractController.php
 * @package MVC
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Http\{Request, Response, Uri};
use \Application\Model\AbstractContent;
use \Application\View\AbstractView;

abstract class AbstractController
{

  /**
   *
   * @var string Controller name (alias content type, as Page or Article)
   *
   */
  protected string $name;

  /**
   *
   * @var string action name (for example 'read', 'update', 'delete')
   *
   */
  protected string $action;

  /**
   *
   * @var Request current request object
   *
   */
  protected Request $request;

  /**
   *
   * @var Response current response object
   *
   */
  protected Response $response;

  /**
   *
   * @var AbstractContent current model object
   *
   */
  protected AbstractContent $model;

  /**
   *
   * @var \Dominicus75\Templater\Skeleton view engine instance
   *
   */
  protected \Dominicus75\Templater\Skeleton $view;


  /**
   *
   * @param string $action (for example 'read', 'update', 'delete')
   * @param Request $request
   * @param Response $response
   *
   */
  public function __construct(
    string $action,
    Request $request
  ){
    $this->action  = $action;
    $this->request  = $request;
    $this->response = new Response();
  }

  public function getName(): string { return $this->name; }

  public function getAction(): string { return $this->action; }

  public function getRequest(): Request { return $this->request; }

  public function getResponse(): Response { return $this->response; }

  public function getModel(): AbstractContent { return $this->model; }

  public function getView(): string { return $this->view->render(); }

  abstract public function create(): void;

  abstract public function read(): void;

  abstract public function update(): void;

  abstract public function delete(): void;


}
