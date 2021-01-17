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

class User extends \Application\Core\AbstractController
{

  /**
   *
   * @var \Dominicus75\Model\Entity
   *
   */
  private Entity $session;

  /**
   *
   * @param Router\Route $route current route instance
   * @param array $content (optional)
   *
   *
   */
  public function __construct(Route $route, Request $request){
    $parameters['content_type']  = 'user';
    $parameters['content_table'] = 'users';
    parent::__construct($route, $parameters, $request);
  }

  public function login() {
    $post = $this->request->getParsedBody();
    if(empty($post)) {
      $this->layout = new \Application\View\Entrance(['action' => 'login']);
      $this->layout->bindValue('{{url}}', 'http://'.$_SERVER['SERVER_NAME'].$this->request->getUri());
      $this->layout->bindValue('{{title}}', 'Belépés');
    } else {
      $this->session = new Entity('session', 'sessions', $this->model->getDatabase());
      $salt = Authority::SALT;
      $pass = hash('sha512', $salt.$post['pass'].$salt);
      $user = $this->model->selectData(
        ['email', $post['email']],
        ['id', 'name', 'email', 'avatar'],
        [
          ['AND', 'pass', '=', $pass],
          ['AND', 'status', '=', 1]
        ]
      );
      if($user) {
        Session::set('user', 'sid', 'user-'.session_id());
        Session::set('user', 'id', $user['id']);
        Session::set('user', 'name', $user['name']);
        Session::set('user', 'email', $user['email']);
        Session::set('user', 'avatar', $user['avatar']);
        Session::set('user', 'spass', hash('sha512', $_SESSION['user']['sid'].$salt.$user['id']));
        $this->session->setProperties([
          'sid'   => Session::get('user', 'sid'),
          'uid'   => Session::get('user', 'id'),
          'spass' => Session::get('user', 'spass'),
          'stime' => time()
        ]);
        $this->success = $this->session->insertData();
        $this->redirect = $this->success ? '/' : '/user/login.html';
      }
    }
  }

  public function logout() {
    $this->session = new Entity('session', 'sessions', $this->model->getDatabase());
    $this->session->deleteData(['sid', Session::get('user', 'sid')]);
    Session::destroy();
    $this->success = true;
    $this->redirect = '/';
  }

  public function register() {
    $post = $this->request->getParsedBody();
    if(empty($post)) {
      $this->layout = new \Application\View\Entrance(['action' => 'register']);
      $this->layout->bindValue('{{url}}', 'http://'.$_SERVER['SERVER_NAME'].$this->request->getUri());
      $this->layout->bindValue('{{title}}', 'Regisztráció');
    } else {
      $user = $this->model->selectData(
        [null, null],
        ['name', 'email'],
        [
          ['AND', 'name', '=', $post['name']],
          ['AND', 'email', '=', $post['email']]
        ]
      );
      if($user) {
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
          'pass'   => $pass,
          'status' => 0
        ]);
        $this->success  = $this->model->insertData();
        $this->redirect = $this->success ? '/user/confirm.html' : '/user/register.html' ;
      }
    }
  }

  public function confirm() {
    $post = $this->request->getParsedBody();
    if(empty($post)) {
      $this->layout = new \Application\View\Entrance(['action' => 'confirm']);
      $this->layout->bindValue('{{url}}', 'http://'.$_SERVER['SERVER_NAME'].$this->request->getUri());
      $this->layout->bindValue('{{title}}', 'Regisztráció megerősítése');
    } else {
      $salt = Authority::SALT;
      $pass = hash('sha512', $salt.$post['pass'].$salt);
      $user = $this->model->selectData(
        ['name', $post['name']],
        ['id', 'name', 'email', 'status'],
        [
          ['AND', 'email', '=', $post['email']],
          ['AND', 'pass', '=', $pass]
        ]
      );
      if($user) {
        if($user['status'] != '1') {
          $this->model->updateProperty('status', '1');
          $this->model->updateData(['id', $user['id']]);
          $variables['title']       = 'Sikeres regisztráció';
          $variables['description'] = 'Regisztráció megerősítve. Most már <a href=\'/user/login.html\'>beléphet</a>';
          $variables['image']       = '/images/successful.jpg';
        } else {
          $variables['title']       = 'Hiba';
          $variables['description'] = 'Már aktivált felhasználó';
          $variables['image']       = '/images/failure.jpg';
        }
      } else {
        $variables['title']       = 'Hiba';
        $variables['description'] = 'Még nincs ilyen felhasználó, de még <a href="/user/register.html">lehet</a>';
        $variables['image']       = '/images/failure.jpg';
      }
      $parameters['type']   = 'message';
      $parameters['action'] = 'view';
      $this->layout  = new \Application\View\View($parameters);
      $this->layout->updateVariables($variables);
      $this->success = true;
    }
  }

}
