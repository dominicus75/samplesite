<?php
/*
 * @file Page.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View;

use \Application\Core\AbstractView;
use \Dominicus75\Templater\{
  Skeleton,
  DirectoryNotFoundException,
  FileNotFoundException
};

class Page extends Site
{

  /**
   * Constructor of class Page.
   *
   * @return void
   */
  public function __construct(string $action, array $content)
  {

    try {

      $view = new Skeleton(CSS);
      parent::__construct($action, $view, $content);
      $this->view->insertHead($action);
      $this->view->assignComponent('%%header%%', TPL, 'header.tpl');
      $model = new \Application\Model\Nav();
      $this->view->insertNav($model->getPages(), $model->getCategories(), TPL.'nav'.DSR);


      $this->view->assignSource('@@header@@', 'header.tpl');
      $nav = new \Application\Element\Nav();
      $this->view->assignTemplate('@@nav@@', $nav->render());
      $this->view->assignSource('@@aside@@', 'aside.tpl');
      $this->view->assignSource('@@main@@', 'page'.DSR.$this->action.'.tpl');
      $this->view->assignSource('@@footer@@', 'footer.tpl');
      $this->view->buildLayout();

      $this->view->setVariables([
        '{{title}}' => $content['title'],
        '{{description}}' => $content['description'],
        '{{url}}' => $content['url'],
        '{{site_name}}' => $content['site_name'],
        '{{locale}}' => $content['locale'],
        '{{image}}' => '/upload/images/'.$content['image'],
        '{{row}}' => $content['aside'],
        '{{body}}' => $content['body']
      ]);

    } catch(DirectoryNotFoundException | FileNotFoundException $e) {
      echo $e->getMessage();
    }

  }

}
