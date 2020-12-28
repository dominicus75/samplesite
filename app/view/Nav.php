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
var_dump($this->buildPages());
var_dump($this->buildCategories($this->categories));
    } catch(\Dominicus75\Templater\FileNotFoundException|
            \InvalidArgumentException $e) { echo '<p>'.$e->getMessage().'</p>'; }
  }


  private function buildCategories(array $categories, string $result = ''): string {

    foreach($categories as $category) {
      if(is_null($category['child'])) {
        $tpl = new IterativeTemplate($this->view->templateDirectory.'childItem.tpl');
        $tpl->setVariables($category['link']);
        $result .= $tpl->render().PHP_EOL;
      } else {
        $tpl = new IterativeTemplate($this->view->templateDirectory.'parentItem.tpl');
        $tpl->setVariables($category['link']);
        $result .= $tpl->render().PHP_EOL;
        if(array_key_exists('child', $category['child'])) {
          $tpl = new IterativeTemplate($this->view->templateDirectory.'childItem.tpl');
          $tpl->setVariables($category['child']['link']);
          $sub = $tpl->render().PHP_EOL;
          $result = str_replace('@@child@@', $sub, $result);
        } else {
          $sub = $this->buildCategories($category['child'], $result);
        }
      }
    }

    return $result;

  }

  private function buildPages(): string {

    $result = '';

    foreach($this->pages as $page) {
      $tpl = new IterativeTemplate($this->view->templateDirectory.'childItem.tpl');
      $tpl->setVariables($page);
      $result .= $tpl->render().PHP_EOL;
    }

    return $result;

  }

  private function buildMenu() {
        $tpl = new IterativeTemplate($this->view->templateDirectory.'parentItem.tpl');
        $tpl->setVariables($category['link']);
        $result[$id] = $tpl->render();

  }



  public function render(): string { return $this->view->render(); }

}
