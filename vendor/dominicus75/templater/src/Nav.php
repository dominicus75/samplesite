<?php
/*
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Nav extends Component {

  /**
   *
   * @var array list of pages
   *
   */
  private array $pages;

  /**
   *
   * @var array recursive list of categories
   *
   */
  private array $categories;

  /**
   *
   * @param array $menu list of pages and recursive list of categories (with subcategories)
   * in form ['pages' => [], 'categories' => []]
   * @param string $templateDirectory Fully qualified path name of tpl root e. g. '/theme/default/'
   *
   * @throws \Dominicus75\Templater\Exceptions\DirectoryNotFoundException
   * if $templateDirectory does not exists
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException
   * if nav tpl file does not exists
   *
   */
  public function __construct(
    array $menu,
    string $templateDirectory = ''
  ){

    try {
      parent::__construct('nav.tpl', $templateDirectory);
      $this->pages      = $menu[0];
      $this->categories = $menu[1];
      $this->source     = empty($menu[1]) ? $this->buildPages() : $this->buildMenu();
      $this->assignText('@@menu@@', $this->getSource());
    } catch(Exceptions\DirectoryNotFoundException | Exceptions\FileNotFoundException $e) { throw $e; }

  }


  private function buildPages(): string {

    $result = '';

    foreach($this->pages as $page) {
      $tpl = new RenderableSource($this->templateDirectory.'navChildItem.tpl');
      $tpl->setVariables($page);
      $tpl->render();
      $result .= '      '.$tpl->getSource().PHP_EOL;
    }

    return $result;

  }


  private function buildCategories(array $categories, string $result = '', string $indent = ' '): string {

    $quantity = count($categories);

    if($quantity == 0) {
      return '';
    } elseif($quantity == 1) {
      $key = array_key_first($categories);
      $category = $categories[$key];
      if(is_null($category['child'])) {
        $tpl = new RenderableSource($this->templateDirectory.'navChildItem.tpl');
        $tpl->setVariables($category['link']);
        $tpl->render();
        $result .= $tpl->getSource();
      } else {
        $tpl = new Component('navParentItem.tpl', $this->templateDirectory);
        $tpl->setVariables($category['link']);
        $child = $this->buildCategories($category['child'], $result, $indent);
        $tpl->assignText('@@child@@', $child);
        $tpl->render();
        $result .= $indent.$tpl->getSource().PHP_EOL;
      }
    } else {
      foreach($categories as $id => $category) {
        $indent .= $indent.$indent;
        $result .= $indent.$this->buildCategories($category['child'], $indent);
      }
    }

    return $result;

  }


  private function buildMenu(): string {
    $menu  = $this->buildPages();
    $menu .= $this->buildCategories($this->categories);
    return $menu;
  }

}
