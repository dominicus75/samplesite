<?php
/*
 * @file Aside.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View;

use \Dominicus75\MNB\Currencies;
use \Dominicus75\Templater\TemplateIterator;

class Aside
{

  /**
   *
   * @var string Fully qualified path name of template directory
   *
   */
  private string $templateDirectory = TPL.'aside'.DSR;

  /**
   *
   * @var string Fully qualified path name of table template file (tpl)
   *
   */
  private string $tableTemplateUrl = 'aside.tpl';

  /**
   *
   * @var string Fully qualified path name of table row template file (tpl)
   *
   */
  private string $rowTemplateUrl = 'row.tpl';

  /**
   *
   * @var string in form '&&marker&&'
   *
   */
  private string $marker = '&&row&&';

  /**
   *
   * @var \Dominicus75\Templater\TemplateIterator
   *
   */
  private \Dominicus75\Templater\TemplateIterator $iterator;


  /**
   * Constructor of class SOAP.
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
      $this->iterator = new TemplateIterator(
        $this->templateDirectory.$this->tableTemplateUrl,
        $this->templateDirectory.$this->rowTemplateUrl,
        $this->marker,
        $content
      );
    } catch(\Dominicus75\Templater\FileNotFoundException|
            \InvalidArgumentException $e) { echo $e->getMessage(); }
  }

  public function renderTemplate(): string { return $this->iterator->render(); }


}
