<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Element;

use \Dominicus75\MNB\Currencies;
use \Dominicus75\Templater\{TemplateLooper, ItemTemplate};

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
   * @var \Application\View\MNB
   *
   */
  private \Application\View\MNB $view;

  /**
   * Constructor of class MNB.
   *
   * @return void
   */
  public function __construct(string $wsdl)
  {
    try {

      $model = new Currencies($wsdl);
      $content = [];

      foreach($this->currencies as $code) {
        $currency = $model->getCurrency($code);
        $content[] = ['{{code}}' => $code, '{{unit}}' => $currency->unit, '{{value}}' => $currency->value];
      }

      $this->view = new \Application\View\MNB($content);

    } catch (\SoapFault $e) { echo $e->getMessage(); }
  }

  public function render() { return $this->view->render(); }

}
