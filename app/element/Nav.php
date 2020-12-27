<?php
/*
 * @file Nav.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Element;

class Nav
{

  /**
   *
   * @var \Application\View\Nav
   *
   */
  private \Application\View\Nav $view;

  /**
   * Constructor of class Nav.
   *
   * @return void
   */
  public function __construct()
  {
    try {
      $model = new \Application\Model\Nav();
      $this->view = new \Application\View\Nav($model->getPages(), $model->getCategories());
      var_dump($this);
    } catch(\PDOException $e) {
      echo '<p>'.$e->getMessage().'</p>';
    }
  }

  // ...

}
