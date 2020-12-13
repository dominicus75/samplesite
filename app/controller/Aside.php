<?php
/*
 * @file Aside.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\MNB\Currencies;
use \Dominicus75\Templater\{TemplateLooper, ItemTemplate};

class Aside
{

  /**
   *
   * @var \Application\View\Aside
   *
   */
  private \Application\View\Aside $view;

  /**
   *
   * @var array given currencies
   *
   */
  private array $currencies = [
    'EUR', 'USD', 'AUD', 'CAD', 'CHF', 'GBP', 'HRK', 'RUB', 'UAH', 'RON'
  ];


  /**
   * Constructor of class SOAP.
   *
   * @param string $wsdl fully qualified uri of wsdl file
   * for example: 'http://www.mnb.hu/arfolyamok.asmx?wsdl'
   *
   * @throws \SoapFault
   *
   * @return void
   */
  public function __construct(
    string $wsdl
  ){

    try {

      $model = new Currencies($wsdl);
      $content = [];

      foreach($this->currencies as $code) {
        $currency = $model->getCurrency($code);
        $content[] = ['{{code}}' => $code, '{{unit}}' => $currency->unit, '{{value}}' => $currency->value];
      }

      $this->view = new \Application\View\Aside($content);

    } catch (\SoapFault $e) { echo $e->getMessage(); }

  }


  public function renderView(): string {
    return $this->view->renderTemplate();
  }

}
