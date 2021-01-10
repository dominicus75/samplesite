<?php
/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View\Admin;

use \Dominicus75\Config\Config;
use \Dominicus75\Templater\Exceptions\{
  DirectoryNotFoundException,
  FileNotFoundException,
  MarkerNotFoundException,
  NotRenderableException
};

class Dashboard extends \Dominicus75\Templater\Layout
{

  /**
   * Constructor of class Admin.
   * @throws Dominicus75\Templater\Exceptions\FileNotFoundException if $sourceFile does not exists
   * @throws Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @return void
   */
  protected function __construct(array $parameters, array $variables = [])
  {

    try {

      parent::__construct(new Config('dashboard_view'), ATPL, 'admin.html');

      $this->assignBufferedComponent('%%head%%');
      if($parameters['action'] !== 'login') {
        $this->assignComponent('%%nav%%', ['file' => 'nav.tpl']);
        $this->assignComponent('%%article%%', ['file' => $parameters['type'].DSR.'view.tpl']);
        $this->bindValue('{{background}}', '/images/admin-bg.jpg');
        $this->bindValue('{{avatar}}', $_SESSION['admin']['avatar']);
        $this->bindValue('{{id}}', $_SESSION['admin']['id']);
        $this->bindValue('{{title}}', 'Üdv!');
        $this->bindValue('{{user}}', $_SESSION['admin']['name']);
        if($parameters['script']) {
          $this->assignComponent('%%script%%', ['file' => 'script.tpl']);
        } else { $this->assignComponent('%%script%%'); }
      } else {
        $this->assignComponent('%%nav%%');
        $this->assignComponent('%%article%%', ['file' => $parameters['type'].DSR.'login.tpl']);
        $this->bindValue('{{title}}', 'Belépés');
        $this->bindValue('{{background}}', '/images/dubai-city.jpg');
        $this->assignComponent('%%script%%');
      }
    } catch(DirectoryNotFoundException | FileNotFoundException | MarkerNotFoundException $e) {
      echo $e->getMessage();
    }

  }

}
