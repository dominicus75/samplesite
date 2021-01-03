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
   * component, template source, and variables
   * - source: unrendered tpl file, what contains html sources and variable marker
   * - component: rendered html page element (e. g. head, nav or footer),
   * what contains html sources, rendered sources and variable marker
   * - variable: text content, what does not contains markers
   *
   */
  const MARKERS = [
    'source' => '/@@[a-zA-Z0-9_-]+@@/is',
    'component' => '/%%[a-zA-Z0-9_-]+%%/is',
    'variable' => '/{{[a-zA-Z0-9_-]+}}/is'
  ];

  /**
   *
   * @static string default Fully qualified path name of template directory
   *
   */
  const DIR = __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;

  /**
   *
   * @static default template filenames
   *
   */
  const FILES = [
    'skeleton' => self::DIR.'skeleton.html',
    'head' => self::DIR.'head.tpl',
    'header' => self::DIR.'header.tpl',
    'nav' => self::DIR.'nav.tpl',
    'aside' => self::DIR.'aside.tpl',
    'main' => self::DIR.'main.tpl',
    'footer' => self::DIR.'footer.tpl',
    'script' => self::DIR.'script.tpl'
  ];

}
