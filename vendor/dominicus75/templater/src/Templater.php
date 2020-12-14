<?php
/*
 * @file Templater.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;

class Templater
{

  /**
   *
   * @static string Regular expressions to validate
   * template file, variable and foreach markers
   *
   */
  const MARKERS = [
    'template' => '/@@[a-zA-Z0-9_-]+@@/is',
    'variable' => '/{{[a-zA-Z0-9_-]+}}/is'
  ];

}
