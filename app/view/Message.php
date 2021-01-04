<?php
/*
 * @file Fault.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View;

use \Dominicus75\Templater\{
  Skeleton,
  DirectoryNotFoundException,
  FileNotFoundException
};
use \Dominicus75\Core\AbstractView;

class Message extends AbstractView
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
      $this->view->insertHead('message');
      $this->view->assignComponent('%%header%%', TPL, 'header.tpl');
//      $model = new \Application\Model\Nav();
//      $this->view->insertNav($model->getPages(), $model->getCategories(), TPL.'nav'.DSR);

      $this->view->assignComponent('%%aside%%', '');
      $this->view->assignComponent('%%main%%', 'message'.DSR.$this->action.'.tpl');
      $this->view->assignComponent('%%footer%%', 'footer.tpl');

      $this->view->setVariables([
        '{{title}}' => $content['title'],
        '{{description}}' => $content['description'],
        '{{image}}' => $content['image']
      ]);

    } catch(DirectoryNotFoundException | FileNotFoundException $e) { throw $e; }

  }

}
