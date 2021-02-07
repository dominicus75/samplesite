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

class Install extends \Application\Core\AbstractController
{

  /**
   *
   * @param Router\Route $route current route instance
   * @param Request $request current request instance
   *
   *
   */
  public function __construct(Route $route, Request $request){
    if(!is_file(APP.'config'.DSR.'installed.php')) {
      $this->layout = new \Application\View\Entrance([
        'type'   => 'install',
        'action' => $route->method,
        'script' => false
      ]);
      parent::__construct($route, [], $request);
    } else {
      $this->success  = true;
      $this->redirect = '/';
    }
  }


  public function start() {
    $this->layout->bindValue('{{title}}', 'Telepítő');
  }


  public function database() {
    $post = $this->request->getParsedBody();
    if(empty($post)) {
      $this->layout->bindValue('{{url}}', 'http://'.$_SERVER['SERVER_NAME'].$this->request->getUri());
      $this->layout->bindValue('{{title}}', 'Adatbáziskapcsolat beállítása');
    } else {
      $input = [
        'datasource' => 'mysql:dbname='.$post['dbname'].';host='.$post['host'],
        'username' => $post['username'],
        'password' => $post['password'],
        'options' => null
      ];
      $config = new \Dominicus75\Config\Config('mysql', APP.'config', $input);
      if($config->save()) {
        $pdo = PDO::getInstance($config);
        $sql = file_get_contents(PRD.'samplesite.sql');
        $pdo->exec($sql);
        $this->success  = true;
        $this->redirect = '/admin/install/root.html';
      }
    }
  }


  public function root() {
    $post = $this->request->getParsedBody();
    if(empty($post)) {
      $this->layout->bindValue('{{url}}', 'http://'.$_SERVER['SERVER_NAME'].$this->request->getUri());
      $this->layout->bindValue('{{title}}', 'Superadmin (root felhasználó) létrehozása');
    } else {
      $this->model = new Entity('admin', 'admins', new \Dominicus75\Config\Config('mysql'));
      $superadmin = $this->model->selectData(
        [null, null],
        ['name', 'email', 'rank'],
        [
          ['AND', 'name', '=', $post['name']],
          ['AND', 'email', '=', $post['email']],
          ['AND', 'rank', '=', 'root']
        ]
      );
      if($superadmin) {
        $variables['title']       = 'Hiba';
        $variables['description'] = 'Már létezik a root felhasználó!';
        $variables['image']       = '/images/failure.jpg';
        $this->layout->updateVariables($variables);
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
        if($this->success) {
          $data = file_get_contents(PRD.'samplesite-data.sql');
          $pdo  = PDO::getInstance(new \Dominicus75\Config\Config('mysql'));
          $pdo->exec($data);
          $this->success  = true;
          $config = new \Dominicus75\Config\Config('installed', APP.'config', ['installed' => true]);
          if($config->save()) {
            $this->redirect = '/admin/login.html';
          }
        } else {
          $this->redirect = '/admin/install/root.html';
        }
      }
    }
  }

}
