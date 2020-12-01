<?php
/*
 * @file Page.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\VariousTools\Config;


class Page extends \Dominicus75\MVC\AbstractController
{

  public function __construct(
    Object $request,
    string $action,
    string $contentID
  ){

    parent::__construct($request, $action, $contentID);
    $this->name  = 'Page';
    $this->model = new \Application\Model\Page(new Config('pdo'), $this->contentID);
    //$this->view  = new \Application\View\Page();


  }

  public function getName(): string {

  }

  public function getRequest(): Object {}

  public function getAction(): string {}

  public function getContentId(): string {}

  public function getView(): \Dominicus75\MVC\AbstractView {}

  public function getModel(): \Dominicus75\MVC\AbstractModel {}


}
