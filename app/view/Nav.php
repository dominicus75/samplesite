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
      $this->view->assignTemplate('@@menu@@', $this->buildMenu());
      $this->view->buildLayout();
    } catch(\Dominicus75\Templater\FileNotFoundException|
            \InvalidArgumentException $e) { echo '<p>'.$e->getMessage().'</p>'; }
  }


  private function buildCategories(array $categories, string $result = ''): string {

    foreach($categories as $id => $category) {
      if(!is_null($category['child'])) {
        $tpl = new IterativeTemplate($this->view->templateDirectory.'parentItem.tpl');
        $tpl->setVariables($category['link']);
        $result .= '  '.$tpl->render().PHP_EOL;
        $result = $this->buildCategories($category['child'], $result);
      } else {
        $tpl = new IterativeTemplate($this->view->templateDirectory.'childItem.tpl');
        $tpl->setVariables($category['link']);
        $child = "  ".$tpl->render();
        $result = str_replace('@@child@@', $child, $result);
      }
    }

    return $result;

  }

  private function buildPages(): string {

    $result = '';

    foreach($this->pages as $page) {
      $tpl = new IterativeTemplate($this->view->templateDirectory.'childItem.tpl');
      $tpl->setVariables($page);
      $result .= '      '.$tpl->render().PHP_EOL;
    }

    return $result;

  }

  private function buildMenu(): string {
    $menu  = $this->buildPages();
    $menu .= $this->buildCategories($this->categories);
    return $menu;
  }


  public function render(): string { return $this->view->getSource(); }

}
