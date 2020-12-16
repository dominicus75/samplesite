<?php
/*
 * @file Page.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Http\{Request, Response, Uri};
use \Dominicus75\VariousTools\Config;

class Page extends AbstractController
{

  public function __construct(
    string $url,
    string $action,
    Request $request
  ){

    parent::__construct($action, $request);

    $this->name  = 'Page';
    $this->model = new \Application\Model\Page(new Config('pdo'), $url);
    $this->view  = new \Dominicus75\Templater\Skeleton(
      dirname(__DIR__).DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'templates',
      'skeleton.html'
    );


    $this->view->assignTemplateSource('@@head@@', 'head.tpl');
    $this->view->assignTemplateSource('@@header@@', 'header.tpl');
    $this->view->assignTemplateSource('@@nav@@', 'nav.tpl');
    $this->view->assignTemplateSource('@@aside@@', 'aside.tpl');
    $this->view->assignTemplateSource('@@main@@', 'page'.DSR.$this->action.'.tpl');
    $this->view->assignTemplateSource('@@footer@@', 'footer.tpl');
    $this->view->buildLayout();

  }


  public function create(): void {




  }


  public function read(): void {

    $content = $this->model->getContent();
    $aside = new \Application\Element\Aside('http://www.mnb.hu/arfolyamok.asmx?wsdl');

    $this->view->assignTemplateIterator(
      'navItem.tpl',
      '{{nav}}',
      [
        ['{{url}}' => '/', '{{target}}' => 'Kezdőlap'],
        ['{{url}}' => '/rolunk.html', '{{target}}' => 'Rólunk'],
        ['{{url}}' => '/kapcsolat.html', '{{target}}' => 'Kapcsolat']
      ]
    );

    $this->view->setVariables([
      '{{title}}' => $content['title'],
      '{{description}}' => $content['description'],
      '{{url}}' => $content['url'],
      '{{site_name}}' => 'Globetrotter',
      '{{locale}}' => 'hu_HU',
      '{{type}}' => $content['type'],
      '{{image_url}}' => 'image',
      '{{row}}' => $aside->renderView(),
      '{{body}}' => $content['body']
    ]);

    $this->response->setBody($this->getView());
    $this->response->send();

  }


  public function update(): void {


  }


  public function delete(): void {


  }


}
