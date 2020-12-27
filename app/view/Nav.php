<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View;

use \Dominicus75\Templater\{
  Component,
  IterativeTemplate,
  TemplateIterator,
  DirectoryNotFoundException,
  FileNotFoundException
};

class Nav
{

  /**
   *
   * @var array list of pages
   *
   */
  private array $pages;

  /**
   *
   * @var array list of categories
   *
   */
  private array $categories;

  /**
   *
   * @var \Dominicus75\Templater\Component
   *
   */
  private \Dominicus75\Templater\Component $view;

  /**
   * Constructor of class Nav.
   *
   * @return void
   */
  public function __construct(array $pages, array $categories) {

    try {
      $this->view       = new Component(TPL.'nav'.DSR, 'nav.tpl');
      $this->pages      = $pages;
      $this->categories = $categories;
var_dump($this->buildCategories());
    } catch(\Dominicus75\Templater\FileNotFoundException|
            \InvalidArgumentException $e) { echo $e->getMessage(); }
  }


  private function buildCategories(): array {

    $result = [];

    foreach($this->categories as $id => $category) {
      if(!is_null($category['child'])) {
        $tpl = new IterativeTemplate($this->view->templateDirectory.'parentItem.tpl');
        $tpl->bindValue(array_first_key($category),);
        $result[$id] = $category['{{url}}'];//$tpl->render();
      }
    }

    return $result;

  }


  private function buildMenu() {

  }



  public function render(): string { return $this->view->render(); }

}
