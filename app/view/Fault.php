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

class Fault extends AbstractView
{

  /**
   * Constructor of class Page.
   *
   * @return void
   */
  public function __construct(array $content, string $action = 'read')
  {

    try {

      $view = new Skeleton(TPL, CSS, 'skeleton.html');
      parent::__construct($content, $action, $view);
      $action = ($action == 'read') ? '' : $action.'.css';

      $this->view->assignSource('@@head@@', 'head.tpl');
      $this->view->assignSource('@@meta@@', '');
      $this->view->assignCSS('@@common@@', 'common.css');
      $this->view->assignCSS('@@desktop-common@@', 'desktop'.DSR.'common.css');
      $this->view->assignCSS('@@desktop-typified@@', 'desktop'.DSR.'fault.css');
      $this->view->assignCSS('@@desktop-action@@', $action);
      $this->view->assignCSS('@@laptop-common@@', 'laptop'.DSR.'common.css');
      $this->view->assignCSS('@@laptop-typified@@', 'laptop'.DSR.'fault.css');
      $this->view->assignCSS('@@laptop-action@@', $action);
      $this->view->assignCSS('@@tablet-common@@', 'tablet'.DSR.'common.css');
      $this->view->assignCSS('@@tablet-typified@@', 'tablet'.DSR.'fault.css');
      $this->view->assignCSS('@@tablet-action@@', $action);
      $this->view->assignCSS('@@mobile-common@@', 'mobile'.DSR.'common.css');
      $this->view->assignCSS('@@mobile-typified@@', 'mobile'.DSR.'fault.css');
      $this->view->assignCSS('@@mobile-action@@', $action);

      $this->view->assignSource('@@header@@', 'header.tpl');
      $nav = new \Application\Element\Nav();
      $this->view->assignTemplate('@@menu@@', $nav->render());
      $this->view->assignSource('@@aside@@', '');
      $this->view->assignSource('@@main@@', 'fault'.DSR.$this->action.'.tpl');
      $this->view->assignSource('@@footer@@', 'footer.tpl');
      $this->view->buildLayout();

      $this->view->setVariables([
        '{{title}}' => $content['title'],
        '{{description}}' => $content['description'],
        '{{image}}' => $content['image'],
        '{{error}}' => $content['message']
      ]);

    } catch(DirectoryNotFoundException | FileNotFoundException $e) { throw $e; }

  }

}
