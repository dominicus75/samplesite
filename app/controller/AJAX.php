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
  PDO,
  Entity,
  ContentNotFoundException,
  InvalidFieldNameException,
  InvalidStatementException
};
use \Application\Core\{Authority, Session};

class AJAX extends \Application\Core\AbstractController
{

  /**
   *
   * @param Router\Route $route current route instance
   * @param array $content (optional)
   *
   *
   */
  public function __construct(Route $route, Request $request) {
    parent::__construct($route, $parameters = [], $request);
  }


  public function post() {

    switch($this->route->cid) {
      case 'avatar':
        try {
          $uploaded = new \Application\Model\Image($this->request->getUploadedFiles());
          $avatar   = $uploaded->resize();
          $this->layout = new \Dominicus75\Templater\RenderableSource(CTPL.'avatar_preview.tpl', ['{{new_avatar}}' => $avatar]);
        } catch(\Error $e) {
          $this->layout = new \Dominicus75\Templater\RenderableSource(
            CTPL.'alert.tpl',
            [
              '{{message}}' => $e->getMessage(),
              '{{alert_type}}' => 'danger'
            ]
          );
        }
      break;
    }

  }


}
