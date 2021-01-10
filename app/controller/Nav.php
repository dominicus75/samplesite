<?php

/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


namespace Application\Controller;

use \Dominicus75\Templater\{
  DirectoryNotFoundException,
  FileNotFoundException,
  MarkerNotFoundException
};


class Nav {


  private \Application\Model\Nav $model;
  private \Dominicus75\Templater\Nav $view;

  public function __construct() {
    try {
      $this->model = new \Application\Model\Nav();
      $this->view  = new \Dominicus75\Templater\Nav(
        TPL.'nav'.DSR,
        $this->model->getMenu()
      );
    } catch(\PDOException | DirectoryNotFoundException |
            FileNotFoundException | MarkerNotFoundException $e) { throw $e; }
  }

  public function getNav(): string  { return $this->view->display(); }

}
