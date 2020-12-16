<?php
/*
 * @file Article.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;


class Article extends AbstractContent
{


  public function __construct(
    \ArrayAccess $pdoConfig,
    ?string $url = null
  ){

    parent::__construct($pdoConfig, $url);

  }


}
