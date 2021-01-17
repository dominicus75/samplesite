<?php
/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Core;

use \Dominicus75\Model\Table;
use \Dominicus75\Config\Config;
use \Dominicus75\Validator\Filter;

class Session
{

  /**
   *
   * Start new or resume existing session
   *
   * @param void
   * @return void
   *
   */
  public static function init(): void {
    if(session_id() == '') { session_start(); }
  }

  /**
   *
   * it is triggered when invoking inaccessible or polymorph methods in a static context
   *
   * @throws \BadMethodCallException
   *
   */
  public static function __callStatic($name, $args) {

    if($name == 'set') {
      /**
       *
       * Sets a specific value to a specific key of the $_SESSION superglobal
       *
       * @param int|string $args[0] $_SESSION first dimension's key (e. g. 'usergroup' or "role')
       * @param int|string $args[1] $_SESSION second dimension's key (e. g. 'uid')
       * @param int|string $args[2] value of $_SESSION array's item
       * @return void
       *
       */
      switch(count($args)) {
        case 2: $_SESSION[$args[0]] = $args[1];
        break;
        case 3: $_SESSION[$args[0]][$args[1]] = $args[2];
        break;
        default: throw new \BadMethodCallException($name.' method\'s count of arguments is not proper');
        break;
      }
    } elseif($name == 'get') {
      /**
       *
       * Gets a specific key's value from $_SESSION superglobal
       *
       * @param int|string $args[0] $_SESSION first dimension's key (e. g. 'usergroup' or "role')
       * @param int|string $args[1] $_SESSION second dimension's key (e. g. 'uid')
       * @param int|string $args[2] value of $_SESSION array's item
       * @return void
       *
       */
      switch(count($args)) {
        case 1: return Filter::sanitizeHtml($_SESSION[$args[0]]);
        break;
        case 2: return Filter::sanitizeHtml($_SESSION[$args[0]][$args[1]]);
        break;
        default: throw new \BadMethodCallException($name.' method\'s count of arguments is not proper');
        break;
      }
    } else {
      throw new \BadMethodCallException($name.' method is undefined');
    }

  }

  /**
   *
   * Destroys all data registered to a session
   *
   * @param void
   * @return void
   *
   */
  public static function destroy(): void {
    session_destroy();
    foreach($_SESSION as $key => $value) {
      if(is_array($value)) {
        foreach($value as $index => $item) { unset($_SESSION[$key][$index]); }
      } else { unset($_SESSION[$key]); }
    }
  }

}
