<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\MNB\Currencies;

class MNB
{

  /**
   *
   * @var array given currencies
   *
   */
  private array $currencies = [
    'EUR', 'USD', 'AUD', 'CAD', 'CHF', 'GBP', 'HRK', 'RUB', 'UAH', 'RON'
  ];

  /**
   *
   * @var \Application\View\User\MNB
   *
   */
  private \Application\View\User\MNB $view;

  /**
   * Constructor of class MNB.
   *
   * @return void
   */
  public function __construct()
  {
    try {

      $model = new Currencies();
      $content = [];

      foreach($this->currencies as $code) {
        $currency = $model->getCurrency($code);
        $content[] = ['{{code}}' => $code, '{{unit}}' => $currency->unit, '{{value}}' => $currency->value];
      }

      $this->view = new \Application\View\User\MNB($content);

    } catch (\SoapFault $e) { echo $e->getMessage(); }
  }

  public function display() { return $this->view->display(); }

}
