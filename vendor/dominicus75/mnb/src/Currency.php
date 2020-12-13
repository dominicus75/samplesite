<?php
/*
 * @file Currency.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\MNB;

class Currency
{

  /**
   *
   * @var string triliteral code of currency (e. g. 'HUF', 'USD')
   *
   */
  private string $code;


  /**
   *
   * @var int
   *
   */
  private int $unit;

  /**
   *
   * @var float value of a currency's unit, in HUF
   *
   */
  private float $value;

  /**
   * Constructor of class Currency.
   *
   * @param string $code
   * @param int $unit
   * @param float $value
   *
   * @return void
   */
  public function __construct(
    string $code,
    int $unit,
    float $value
  ){
    $this->code  = $code;
    $this->unit  = $unit;
    $this->value = $value;
  }

  public function __get($name) { return $this->$name; }

}
