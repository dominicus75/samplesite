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

class Dashboard extends \Dominicus75\Templater\Layout
{

  /**
   * Constructor of class Dashboard.
   * @throws Dominicus75\Templater\Exceptions\FileNotFoundException if $sourceFile does not exists
   * @throws Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @return void
   */
  public function __construct(array $parameters, array $variables = [])
  {

    try {
      parent::__construct(new Config('dashboard_view'), ATPL, 'admin.html');
      $parameters['meta']  = $parameters['type'] == 'message' ? false : true;
      $parameters['aside'] = $parameters['type'] == 'message' ? false : true;
      $parameters['user']  = $parameters['type'] == 'message' ? false : true;
      $this->assignBufferedComponent('%%head%%');
      if(is_file(ACSS.$parameters['type'].'_'.$parameters['action'].'.css')) {
        $this->assignSource('@@action@@', ACSS.$parameters['type'].'_'.$parameters['action'].'.css');
      } else { $this->assignSource('@@action@@'); }
      $this->assignComponent('%%nav%%', ['file' => 'nav.tpl']);
      $this->assignComponent('%%article%%', ['file' => $parameters['type'].DSR.$parameters['action'].'.tpl']);
      $this->bindValue('{{background}}', '/images/admin-bg.jpg');
      $this->bindValue('{{avatar}}', $_SESSION['admin']['avatar']);
      $this->bindValue('{{id}}', $_SESSION['admin']['id']);
      $this->bindValue('{{user}}', $_SESSION['admin']['name']);
      if($parameters['script']) {
        $this->assignComponent('%%script%%', ['file' => 'script.tpl']);
      } else { $this->assignComponent('%%script%%'); }
    } catch(DirectoryNotFoundException | FileNotFoundException | MarkerNotFoundException $e) {
      echo $e->getMessage();
    }

  }

}
