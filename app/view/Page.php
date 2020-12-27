<?php
/*
 * @file Page.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View;

use \Dominicus75\Core\AbstractView;
use \Dominicus75\Templater\{
  Skeleton,
  DirectoryNotFoundException,
  FileNotFoundException
};

class Page extends AbstractView
{

  /**
   * Constructor of class Page.
   *
   * @return void
   */
  public function __construct(array $content, string $action)
  {

    try {

      $view = new Skeleton(TPL, CSS, 'skeleton.html');
      parent::__construct($content, $action, $view);
      $action = ($action == 'read') ? '' : $action.'.css';

      $this->view->assignSource('@@head@@', 'head.tpl');
      $this->view->assignSource('@@meta@@', 'meta.tpl');
      $this->view->assignCSS('@@common@@', 'common.css');
      $this->view->assignCSS('@@desktop-common@@', 'desktop'.DSR.'common.css');
      $this->view->assignCSS('@@desktop-typified@@', 'desktop'.DSR.'page.css');
      $this->view->assignCSS('@@desktop-action@@', $action);
      $this->view->assignCSS('@@laptop-common@@', 'laptop'.DSR.'common.css');
      $this->view->assignCSS('@@laptop-typified@@', 'laptop'.DSR.'page.css');
      $this->view->assignCSS('@@laptop-action@@', $action);
      $this->view->assignCSS('@@tablet-common@@', 'tablet'.DSR.'common.css');
      $this->view->assignCSS('@@tablet-typified@@', 'tablet'.DSR.'page.css');
      $this->view->assignCSS('@@tablet-action@@', $action);
      $this->view->assignCSS('@@mobile-common@@', 'mobile'.DSR.'common.css');
      $this->view->assignCSS('@@mobile-typified@@', 'mobile'.DSR.'page.css');
      $this->view->assignCSS('@@mobile-action@@', $action);

      $this->view->assignSource('@@header@@', 'header.tpl');
      $this->view->assignSource('@@nav@@', 'nav.tpl');
      $this->view->assignTemplateIterator(
        'navItem.tpl',
        '@@menu@@',
        $content['nav']
      );
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
