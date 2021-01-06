<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Http\Request;
use \Dominicus75\Config\Config;
use \Dominicus75\Model\{
  ContentNotFoundException,
  InvalidFieldNameException,
  InvalidStatementException
};

class Message
{

  private string $title;
  private \Application\Model\Message $model;
  private \Application\View\Message $view;

  public function __construct(string $title,  string $image = '', string $message = ''){

    try {
      $this->title  = $title;
      if(!empty($message) && !empty($image)) {
        $content['title']       = $title;
        $content['description'] = $message;
        $content['image']       = $image;
      } else {
        $this->model  = new \Application\Model\Message(new Config('mysql'), 'messages');
        $content = $this->model->read(['category' => null, 'cid' => $this->title]);
      }
      $this->view = new \Application\View\Message('view', $content);
    } catch(\Throwable $e) { echo '<p>'.$e->getMessage().'</p>'; }

  }

  public function view(): string {

    try {
      return $this->view->render();
    } catch(ContentNotFoundException $e) { return '<p>'.$e->getMessage().'</p>'; }

  }

  public function create(): string {}
  public function edit(): string {}
  public function delete(): string {}

}
