<?php
/*
 * @file Currencies.php
 * @package MNB
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\MNB;

class Currencies
{

  /**
   *
   * @var array ['year' => 'YYYY', 'month' => 'MM', 'day' => 'DD']
   *
   */
  private array $date = [
    'year' => null,
    'month' => null,
    'day' => null
  ];

  /**
   *
   * @var array items must be an instance of Currency
   *
   */
  private array $currencies;


  /**
   *
   * @param string $wsdl fully qualified uri of wsdl file
   * for example: 'http://www.mnb.hu/arfolyamok.asmx?wsdl'
   *
   * @throws \SoapFault
   *
   */
  public function __construct(string $wsdl)
  {

    try {

      $client = new \SoapClient($wsdl);
      $xml = simplexml_load_string($client->GetCurrentExchangeRates(null)->GetCurrentExchangeRatesResult);
      $date = explode('-', $xml->Day->attributes()->date);
      $this->date['year']  = $date[0];
      $this->date['month'] = $date[1];
      $this->date['day']   = $date[2];

      foreach ($xml->Day->Rate as $rate) {
        $this->assignCurrency(
          (string)$rate->attributes()->curr,
          (int)$rate->attributes()->unit,
          (float)str_replace(',', '.', $rate)
        );
      }

    } catch (\SoapFault $e) { throw $e; }

  }


  /**
   *
   * @param string $code triliteral code of currency (e. g. 'HUF', 'USD')
   * @param int $unit
   * @param float $value value of a currency's unit, in HUF
   *
   */
  public function assignCurrency(
    string $code,
    int $unit,
    float $value
  ): void {
    $this->currencies[$code] = new Currency($code, $unit, $value);
  }


  /**
   *
   * It returns the current date, in array
   * Array format: ['year' => 'YYYY', 'month' => 'MM', 'day' => 'DD']
   *
   * @param void
   * @return array
   *
   */
  public function getDate(): array { return $this->date; }


  /**
   *
   * It returns the specified currency, as instance of Currency class
   *
   * @param string $code triliteral code of currency (e. g. 'HUF', 'USD')
   * @return Currency
   * @throws \Dominicus75\MNB\CurrencyNotFoundException if Currency is not exists
   *
   */
  public function getCurrency(string $code): Currency {

    if(array_key_exists($code, $this->currencies)) {
      return $this->currencies[$code];
    } else {
      throw new CurrencyNotFoundException();
    }

  }


  /**
   *
   * @param void
   * @return array list of currencies, items of array must be an instance of Currency
   *
   */
  public function getCurrencies(): array { return $this->currencies; }


}
