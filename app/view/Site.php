<?php
/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View;

use \Dominicus75\Config\Config;
use \Dominicus75\Templater\Exceptions\{
  DirectoryNotFoundException,
  FileNotFoundException,
  MarkerNotFoundException,
  NotRenderableException
};

class Site extends \Dominicus75\Templater\Layout
{

  /**
   *
   * @var array element controllers in form (string)'name' => (Object) element controller instance
   *
   */
  protected array $elements;

  /**
   * Constructor of class Site.
   * @throws Dominicus75\Templater\Exceptions\FileNotFoundException if $sourceFile does not exists
   * @throws Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @return void
   */
  public function __construct(array $parameters, array $variables = [])
  {

    try {

      parent::__construct(new Config('site_view'), UTPL, 'site.html');

      $head = [
        '@@desktop-typified@@' => UCSS.'desktop'.DSR.$parameters['type'].'.css',
        '@@laptop-typified@@'  => UCSS.'laptop'.DSR.$parameters['type'].'.css',
        '@@tablet-typified@@'  => UCSS.'tablet'.DSR.$parameters['type'].'.css',
        '@@mobile-typified@@'  => UCSS.'mobile'.DSR.$parameters['type'].'.css'
      ];

      if($parameters['meta']) {
        $head['@@meta@@'] = UTPL.'social_meta.tpl';
      } else { $head['@@meta@@'] = ''; }

      $this->updateBufferedComponent('%%head%%', 'sources', $head);
      $this->assignBufferedComponent('%%head%%');
      $this->assignBufferedComponent('%%header%%');

      $this->elements['nav'] = new \Application\Controller\Nav();
      $this->assignComponent('%%nav%%', ['text' => $this->elements['nav']->getNav()]);
      unset($this->elements['nav']);

      if($parameters['aside']) {

        $this->elements['mnb'] = new \Application\Controller\MNB();
        $this->assignComponent('%%aside%%', ['file' => 'aside.tpl']);
        $this->assignText('@@mnb@@', $this->elements['mnb']->display());
        unset($this->elements['mnb']);

        if(isset($_SESSION['user']['spass'])) {
          $this->assignSource('@@user@@', UTPL.'user'.DSR.'user-aside.tpl');
          $this->bindValue('{{avatar}}', $_SESSION['user']['avatar']);
          $this->bindValue('{{id}}', $_SESSION['user']['id']);
          $this->bindValue('{{user}}', $_SESSION['user']['name']);
        } else {
          $this->assignSource('@@user@@', UTPL.'user'.DSR.'visitor-aside.tpl');
        }

      } else { $this->assignComponent('%%aside%%'); }

      $this->assignComponent('%%main%%', ['file' => $parameters['type'].DSR.$parameters['action'].'.tpl']);

      if($parameters['script']) {
        $this->assignComponent('%%script%%', ['file' => 'script.tpl']);
      } else { $this->assignComponent('%%script%%'); }

    } catch(DirectoryNotFoundException | FileNotFoundException | MarkerNotFoundException $e) {
      echo $e->getMessage();
    }

  }

}
