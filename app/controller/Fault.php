<?php
/*
 * @file Fault.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Http\Response;
use \Dominicus75\Core\Config as Config;

class Fault
{

  /**
   *
   * @var int HTTP error code (e. g. 404)
   *
   */
  private int $code;

  /**
   *
   * @var \Dominicus75\Http\Response current response object
   *
   */
  private Response $response;

  public function __construct(int $code,  string $message = ''){

    $this->code     = $code;
    $this->response = new Response();

    try {
      $this->model = new \Application\Model\Fault(new Config('mysql'), 'faults');
      $content = $this->model->read(['cid' => $this->code]);
      $content['message'] = empty($message) ? '' : PHP_EOL."        <p>$message</p>" ;
      $this->view = new \Application\View\Fault($content, 'read');
      $this->response->setStatusCode($this->code);
      $this->response->setBody($this->view->render());
      $this->read();
    } catch(\Throwable $e) { echo '<p>'.$e->getMessage().'</p>'; }

  }

  private function read(): void {
    $this->response->send();
    die();
  }

}
