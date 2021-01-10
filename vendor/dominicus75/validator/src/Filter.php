<?php
/*
 *
 * @package Validator
 * @author Domokos Endre János <domokos.endrejanos@gmail.com>
 * @copyright 2020 Domokos Endre János
 * @license GNU General Public License v3 (https://opensource.org/licenses/GPL-3.0)
 *
 *
 */

namespace Dominicus75\Validator;

class Filter
{

  public static function validateEmail(string $email):bool {
    return (bool)preg_match(Pattern::EMAIL, $email);
  }

  public static function validatePlainText(
    string $text,
    string $pattern = null,
    int $min = 1,
    int $max = 10240
  ):bool {
    $pattern = is_null($pattern) ? Pattern::TEXT : $pattern;
    return (bool)preg_match($pattern, $text);
  }


  public static function sanitizeHtml(string $html, $allowedTags = null):string {
    $tags = is_null($allowedTags) ? '' : $allowedTags;
    return trim(
    strip_tags(
        preg_replace(Pattern::SCRIPT, "", preg_replace(Pattern::PHP, "", $html)),
        $tags
      )
    );
  }

}
