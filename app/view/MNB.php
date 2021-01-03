<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View;

use \Dominicus75\MNB\Currencies;
use \Dominicus75\Templater\{
  Component,
  IterativeTemplate,
  TemplateIterator,
  DirectoryNotFoundException,
  FileNotFoundException
};

class MNB
{

  /**
   *
   * @var string Fully qualified path name of table row template file (tpl)
   *
   */
  private string $rowTemplateUrl = 'row.tpl';

  /**
   *
   * @var \Dominicus75\Templater\Component
   *
   */
  private \Dominicus75\Templater\Component $view;


  /**
   * Constructor of class MNB.
   *
   * @throws \Dominicus75\Templater\FileNotFoundException if the Looper or Item
   * template file does not exists
   * @throws \InvalidArgumentException if foreach marker is invalid or missing
   * @throws \InvalidArgumentException if either $content item is not an array
   *
   */
  public function __construct(array $content)
  {
    try {
      $this->view = new Component(TPL.'mnb'.DSR, 'mnb.tpl');
      $this->view->assignTemplateIterator('row.tpl', '@@rows@@', $content);
      $this->view->buildLayout();
    } catch(\Throwable $e) { echo $e->getMessage(); }
  }

  public function render(): string { return $this->view->render(); }

}
