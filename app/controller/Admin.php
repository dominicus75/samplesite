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

class Admin extends \Application\Core\AbstractController
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
    $parameters['content_type']  = 'admin';
    $parameters['content_table'] = 'admins';
    parent::__construct($route, $parameters, $request);
  }

  public function login() {
    $post = $this->request->getParsedBody();
    if(empty($post)) {
      $this->layout = new \Application\View\Admin\Login();
      $this->layout->bindValue('{{url}}', 'http://'.$_SERVER['SERVER_NAME'].$this->request->getUri());
    } else {
      $this->session = new Entity('session', 'sessions', $this->model->getDatabase());
      $salt = Authority::SALT;
      $pass = hash('sha512', $salt.$post['pass'].$salt);
      $admin = $this->model->selectData(
        ['email', $post['email']],
        ['id', 'name', 'email', 'avatar', 'rank'],
        [
          ['AND', 'pass', '=', $pass],
          ['AND', 'status', '=', 1]
        ]
      );
      if($admin) {
        Session::set('admin', 'sid', 'admin-'.session_id());
        Session::set('admin', 'id', $admin['id']);
        Session::set('admin', 'name', $admin['name']);
        Session::set('admin', 'email', $admin['email']);
        Session::set('admin', 'avatar', $admin['avatar']);
        Session::set('admin', 'rank', $admin['rank']);
        Session::set('admin', 'spass', hash('sha512', $_SESSION['admin']['sid'].$salt.$admin['id']));
        $this->session->setProperties([
          'sid'   => Session::get('admin', 'sid'),
          'aid'   => Session::get('admin', 'id'),
          'spass' => Session::get('admin', 'spass'),
          'stime' => time()
        ]);
        $this->session->insertData();
      }
    }
  }

  public function logout() {
    $this->session = new Entity('session', 'sessions', $this->model->getDatabase());
    $this->session->deleteData(['sid', Session::get('admin', 'sid')]);
    Session::destroy();
  }

  public function dashboard() {
    $this->layout = new \Application\View\Admin\View('admin');
  }

}
