<?php
/*
 * @file Fault.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Core\{
  Config as Config,
  Model\ContentNotFoundException as ContentNotFoundException,
  Model\InvalidFieldNameException as InvalidFieldNameException,
  Model\InvalidStatementException as InvalidStatementException
};

class Fault
{

  private int $code;
  private \Application\Model\Fault $model;
  private \Application\View\Fault $view;

  public function __construct(int $code,  string $message = ''){

    try {
      if(!empty($message)) {
        $parameters['message'] = $message;
      } else { $parameters['message'] = null; }
      $this->code  = $code;
      $this->model = new \Application\Model\Fault(new Config('mysql'), 'faults', $parameters);
      $content = $this->model->read(['category' => null, 'cid' => $this->code]);
      $this->view = new \Application\View\Fault('read', $content);
    } catch(\Throwable $e) { echo '<p>'.$e->getMessage().'</p>'; }

  }

  public function read(): string {

    try {
      return $this->view->render();
    } catch(ContentNotFoundException $e) { return '<p>'.$e->getMessage().'</p>'; }

  }

  public function create(): string {}
  public function view(): string {}
  public function edit(): string {}
  public function delete(): string {}


}
