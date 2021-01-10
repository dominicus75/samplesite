<?php
/*
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

class Message extends \Application\Core\AbstractController
{

  public function __construct(Route $route, array $parameters = []) {
    $parameters['content_type']  = 'message';
    $parameters['content_table'] = 'messages';
    parent::__construct($route, $parameters, null);
  }

  public function view(): void {
    if(isset($this->parameters['title']) && isset($this->parameters['message']) && isset($this->parameters['image'])) {
      $variables['title']       = $this->parameters['title'];
      $variables['description'] = $this->parameters['message'];
      $variables['image']       = $this->parameters['image'];
    } else {
      $variables = $this->model->selectData(
        ['url', $this->route->cid],
        ['title', 'description', 'image'],
        []
      );
    }
    if($this->route->role == 'admin') {
      $this->layout = new \Application\View\Admin\View('message');
    } else {
      $this->layout = new \Application\View\Visitor\View('message');
    }
    $this->layout->updateVariables($variables);
  }

  public function create(): string {}
  public function edit(): string {}
  public function delete(): string {}

}
