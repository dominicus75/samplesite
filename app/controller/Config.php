<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Router\Route;
use \Dominicus75\Http\Request;
use \Application\Core\{Authority, Session};
use \Dominicus75\Model\{PDO, Entity, EntityInterface};

class Config extends \Application\Core\AbstractController
{


  /**
   *
   * @param Router\Route $route current route instance
   * @param Request $request current request instance
   *
   *
   */
  public function __construct(Route $route, Request $request){
    parent::__construct($route, [], $request);
  }


  public function install() {

    $post   = $this->request->getParsedBody();
    $action = is_null($this->route->cid) ? 'intro' : $this->route->cid;
    $this->layout = new \Application\View\Entrance([
      'type'   => 'config',
      'action' => $action,
      'script' => false
    ]);

    if(empty($post)) {

      switch($action) {
        case 'database':
          $this->layout->bindValue('{{url}}', 'http://'.$_SERVER['SERVER_NAME'].$this->request->getUri());
          $this->layout->bindValue('{{title}}', 'Adatbáziskapcsolat beállítása');
        break;
        case 'superadmin':
          $this->layout->bindValue('{{url}}', 'http://'.$_SERVER['SERVER_NAME'].$this->request->getUri());
          $this->layout->bindValue('{{title}}', 'Superadmin (root felhasználó) létrehozása');
        break;
        default:
          $this->layout->bindValue('{{title}}', 'Telepítő');
        break;
      }

    } else {

      switch($action) {
        case 'database':
          $input = [
            'datasource' => 'mysql:dbname='.$post['dbname'].';host='.$post['host'],
            'username' => $post['username'],
            'password' => $post['password'],
            'options' => null
          ];
          $config = new \Dominicus75\Config\Config('mysql', APP.'config', $input);
          $config->save();
          $pdo = PDO::getInstance($config);
          $sql = file_get_contents(PRD.'samplesite.sql');
          $pdo->exec($sql);
          $this->success  = true;
          $this->redirect = '/admin/config/install/superadmin.html';
        break;
        case 'superadmin':
          $this->model = new Entity('admin', 'admins', new \Dominicus75\Config\Config('mysql'));
          $superadmin = $this->model->selectData(
            [null, null],
            ['name', 'email'],
            [
              ['AND', 'name', '=', $post['name']],
              ['AND', 'email', '=', $post['email']],
              ['AND', 'rank', '=', 'root']
            ]
          );
          if($superadmin) {
            $variables['title']       = 'Hiba';
            $variables['description'] = 'A megadott névvel és e-mail címmel már van regisztrált felhasználó';
            $variables['image']       = '/images/failure.jpg';
            $parameters['type']       = 'message';
            $parameters['action']     = 'view';
            $this->layout  = new \Application\View\View($parameters);
            $this->layout->updateVariables($variables);
            $this->success = true;
          } else {
            $salt = Authority::SALT;
            $pass = hash('sha512', $salt.$post['pass'].$salt);
            $this->model->setProperties([
              'name'   => $post['name'],
              'email'  => $post['email'],
              'avatar' => 'default_avatar.png',
              'rank'   => 'root',
              'pass'   => $pass,
              'status' => 1
            ]);
            $this->success  = $this->model->insertData();
            $this->redirect = $this->success ? '/admin/login.html' : '/admin/config/install/superadmin.html' ;
          }
        break;
      }

    }



  }


  public function view() {

    $this->layout = new \Application\View\Dashboard([
      'type'   => 'config',
      'action' => 'view',
      'script' => false
    ]);

  }


  public function edit() {

    $this->layout = new \Application\View\Dashboard([
      'type'   => 'config',
      'action' => 'edit',
      'script' => false
    ]);

  }


}
