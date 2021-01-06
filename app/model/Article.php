<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;

use \Dominicus75\Config\Config;
use \Dominicus75\Model\AbstractModel;

class Article extends AbstractModel
{



  public function __construct(Config $pdoConfig, string $table){

    try {
      parent::__construct($pdoConfig, $table);
    } catch(\PDOException $e) { throw $e; }

  }


  public function create(): bool {

  }

  public function read(array $url): array {

    $content = $this->select(
      ['url', $url['cid']],
      [],
      [['AND', 'category', '=', $url['category']]]
    );

    if(empty($content)) { new Failure(404); }

    $menu = new Nav();
    $content['nav'] = $menu->getPages();
    $aside = new \Application\Element\Aside('http://www.mnb.hu/arfolyamok.asmx?wsdl');
    $content['aside'] = $aside->renderView();
    $content['url'] = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $content['site_name'] = 'Globetrotter';
    $content['locale'] = 'hu_HU';
    return $content;

  }

  public function edit(array $url): bool {

  }

  public function delete(array $url): bool {

  }

}
