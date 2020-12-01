<?php
/*
 * @file AbstractController.php
 * @package MVC
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\MVC;


abstract class AbstractController
{

  protected string $name;
  protected string $action;
  protected string $contentID;
  protected Object $request;
  protected AbstractModel $model;
  protected AbstractView $view;


  public function __construct(
    Object $request,
    string $action,
    string $contentID
  ){
    $this->request = $request;
    $this->action = $action;
    $this->contentID = $contentID;


  }

  abstract public function getName(): string;

  abstract public function getRequest(): Object;

  abstract public function getAction(): string;

  abstract public function getContentId(): string;

  abstract public function getView(): AbstractView;

  abstract public function getModel(): AbstractModel;

}
