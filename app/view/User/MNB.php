<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View\User;

use \Dominicus75\MNB\Currencies;
use \Dominicus75\Templater\Component;
use \Dominicus75\Templater\Exceptions\{
  DirectoryNotFoundException,
  FileNotFoundException,
  MarkerNotFoundException,
  NotRenderableException
};

class MNB extends Component
{

  /**
   * Constructor of class MNB.
   *
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException
   * if given source file does not exists
   *
   */
  public function __construct(array $content)
  {
    try {
      parent::__construct(UTPL.'mnb'.DSR.'mnb.tpl');
      $this->assignRepeater('@@rows@@', UTPL.'mnb'.DSR.'row.tpl', $content);
    } catch(\Throwable $e) { echo $e->getMessage(); }
  }

}
