<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Router\Route;
use \Dominicus75\Http\Request;
use \Dominicus75\Model\{
  PDO,
  Entity,
  ContentNotFoundException,
  InvalidFieldNameException,
  InvalidStatementException
};
use \Application\Core\{Authority, Session};

class Profile extends \Application\Core\AbstractController
{

  /**
   *
   * @var \Dominicus75\Model\Entity
   *
   */
  private Entity $session;

  /**
   *
   * @var array user's data
   *
   */
  private array $user;

  /**
   *
   * @param Router\Route $route current route instance
   * @param array $content (optional)
   *
   *
   */
  public function __construct(Route $route, Request $request) {

    switch($route->role) {
      case 'admin':
        $parameters['content_type']  = 'admin';
        $parameters['content_table'] = 'admins';
        $this->layout = new \Application\View\Dashboard([
          'type'   => 'profile',
          'action' => $route->method,
          'script' => true
        ]);
      break;
      case 'user':
        $parameters['content_type']  = 'user';
        $parameters['content_table'] = 'users';
        $this->layout = new \Application\View\Site([
          'type'   => 'profile',
          'action' => $route->method,
          'script' => true
        ]);
      break;
      default:
        $this->success  = true;
        $this->redirect = '/message/404.html';
        return;
      break;
    }

    parent::__construct($route, $parameters, $request);
    $this->layout->bindValue('{{url}}', 'http://'.$_SERVER['SERVER_NAME'].$this->request->getUri());
    $this->layout->bindValue('{{role}}', $this->route->role);

  }


  public function create() {
    $this->layout->bindValue('{{title}}', 'Új '.$this->route->role.' profil létrehozása');
    if(Session::get($this->route->role, 'rank') == 'root') {
      $post = $this->request->getParsedBody();
      if(empty($post)) {
        $this->layout->assignSource('@@message@@');
        $this->layout->assignRepeater(
          '@@options@@',
          ATPL.'profile'.DSR.'option.tpl',
          [
            ['{{value}}' => 'admin', '{{name}}' => 'Admin'],
            ['{{value}}' => 'editor', '{{name}}' => 'Editor'],
            ['{{value}}' => 'author', '{{name}}' => 'Author']
          ]
        );
      } else {
        $profile = $this->model->selectData(
          [null, null],
          ['name', 'email'],
          [
            ['AND', 'name', '=', $post['name']],
            ['AND', 'email', '=', $post['email']]
          ]
        );
        if($profile) {
          $message = [
            '{{message}}' => 'Már létezik ilyen nevű felhasználó.',
            '{{alert_type}}' => 'danger'
          ];
          $this->layout->assignRenderableSource('@@message@@', CTPL.'alert.tpl', $message);
          $this->layout->assignSource('@@options@@');
        } else {
          $salt = Authority::SALT;
          $pass = hash('sha512', $salt.$post['pass'].$salt);
          $this->model->setProperties([
            'name'   => $post['name'],
            'email'  => $post['email'],
            'avatar' => $post['avatar'],
            'rank'   => $post['rank'],
            'pass'   => $pass,
            'status' => 1
          ]);
          $this->success  = $this->model->insertData();
          $this->redirect = '/admin/dashboard.html';
        }
      }
    } else {
      $message = [
        '{{message}}' => 'Nincs jogosultságod ehhez a művelethez.',
        '{{alert_type}}' => 'danger'
      ];
      $this->layout->assignRenderableSource('@@message@@', CTPL.'alert.tpl', $message);
      $this->layout->assignSource('@@options@@');
    }
  }

  public function edit() {

    $post = $this->request->getParsedBody();
    $this->layout->bindValue('{{title}}', 'Profil szerkesztése');
    $this->layout->bindValue('{{email}}', Session::get($this->route->role, 'email'));
    if(empty($post)) {
      $this->layout->assignSource('@@message@@');
    } else {
      $user = $this->model->selectData(
        ['id', Session::get($this->route->role, 'id')],
        ['id', 'name', 'email', 'avatar', 'pass'],
        []
      );
      $this->model->setProperties([
        'name' => $user['name'],
        'email' => $user['email'],
        'avatar' => $user['avatar'],
        'pass' => $user['pass']
      ]);
      if($post['oldpass'] == '' || hash('sha512', Authority::SALT.$post['oldpass'].Authority::SALT) != $this->model->getProperty('pass')) {
        $message = [
          '{{message}}' => 'A régi jelszó helyes megadása szükséges a módosításhoz',
          '{{alert_type}}' => 'danger'
        ];
      } elseif($post['pass'] != $post['repass']) {
        $message = [
          '{{message}}' => 'A két jelszó nem egyezik',
          '{{alert_type}}' => 'danger'
        ];
      } else {
        if(isset($post['pass']) && $post['pass'] != '') { $post['pass'] = hash('sha512', Authority::SALT.$post['pass'].Authority::SALT); }
        foreach($post as $key => $value) {
          if($value != '') {
            switch($key) {
              case 'pass':
                if($this->model->getProperty('pass') != $value) {
                  $this->model->updateProperty('pass', hash('sha512', Authority::SALT.$value.Authority::SALT));
                }
              break;
              case 'avatar':
                $this->model->updateProperty('avatar', $value);
                rename(dirname(dirname(__DIR__)).'/public/upload/tmp/'.$value, dirname(dirname(__DIR__)).'/public/upload/images/'.$value);
                if(isset($_SESSION[$this->route->role]['avatar'])) {
                  @unlink(dirname(dirname(__DIR__)).'/public/upload/images/'.Session::get($this->route->role, 'avatar'));
                }
                Session::set($this->route->role, 'avatar', $value);
              break;
              default:
                if($this->model->hasProperty($key) && $this->model->getProperty($key) != $value) { $this->model->updateProperty($key, $value); }
              break;
            }
          }
        }
        if($this->model->updateData(['id', Session::get($this->route->role, 'id')])) {
          $message = [
            '{{message}}' => 'A változtatások mentése megtörtént',
            '{{alert_type}}' => 'success'
          ];
        }
        $this->layout->assignRenderableSource('@@message@@', CTPL.'alert.tpl', $message);
      }
    }

  }



}
