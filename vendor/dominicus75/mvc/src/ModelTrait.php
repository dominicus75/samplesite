<?php
/*
 *
 * @package Mvc
 * @author Domokos Endre János <domokos.endrejanos@gmail.com>
 * @copyright 2020 Domokos Endre János
 * @license GNU General Public License v3 (https://opensource.org/licenses/GPL-3.0)
 *
 *
 */

namespace Dominicus75\Mvc;

use \Dominicus75\Collection\DataSet as DataSet;
use \Dominicus75\TextFile\Ini\{Reader, Writer, Eraser};

trait ModelTrait
{

  protected $url;
  protected $dataSet;

  public function __construct(string $url, $data = null){

    $this->url = $url;

    if(is_null($data)){
      $dataset  = Reader::readFile($this->url);
    } elseif($data instanceof DataSet) {
      $dataset  = $data;
    } elseif(is_array($data)) {
      $dataset  = new DataSet($data);
    } else { throw new \InvalidArgumentException(); }

    $this->dataSet = $dataset;

  }

  public function getDataSet():DataSet { return $this->dataSet; }

  public function save():bool { return Writer::writeFile($this->url, $this->dataSet); }

}
