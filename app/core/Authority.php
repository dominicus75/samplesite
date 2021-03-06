<?php
/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Core;

use \Dominicus75\Model\PDO;
use \Dominicus75\Config\Config;
use \Dominicus75\Router\Route;

class Authority
{

  const SALT = 'H2SO4';

  /**
   *
   * @var \Dominicus75\Model\PDO instance
   *
   */
  private PDO $pdo;

  /**
   *
   * @var int current timestamp
   *
   */
  private int $now;

  /**
   *
   * @var int
   *
   */
  private int $expired;

  /**
   *
   * @var Route
   *
   */
  private Route $route;

  /**
   * Constructor of class Authority.
   *
   * @param string $role user's role (admin|visitor)
   *
   * @return void
   */
  public function __construct(Route $route)  { $this->route = $route; }


  public function authenticate(): bool {

    if($this->route->role == 'visitor') {
      return (preg_match("/(Article|Category|Message|Page)$/", $this->route->controller) && $this->route->method == 'view');
    } elseif($this->route->role == 'admin' && $this->route->controller == '\Application\Controller\Install') {
      return true;
    } else {
      $this->now     = time();
      $this->expired = $this->now - 600;
      try {
        $this->pdo = PDO::getInstance(new Config('mysql'));
        Session::init();
        Session::set('role', $this->route->role);
        $sql = "SELECT stime FROM sessions WHERE sid = :sid AND spass = :spass LIMIT 1";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':sid', $_SESSION[$this->route->role]['sid'], PDO::PARAM_STR);
        $statement->bindParam(':spass', $_SESSION[$this->route->role]['spass'], PDO::PARAM_STR);
        $statement->execute();
        $session = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$session || $session['stime'] < $this->expired) {
          $sql = "DELETE FROM sessions WHERE sid = :sid AND spass = :spass LIMIT 1";
          $statement = $this->pdo->prepare($sql);
          $statement->bindParam(':sid', $_SESSION[$this->route->role]['sid'], PDO::PARAM_STR);
          $statement->bindParam(':spass', $_SESSION[$this->route->role]['spass'], PDO::PARAM_STR);
          $statement->execute();
          Session::destroy();
          return false;
        } else {
          $sql = "UPDATE sessions SET stime = :now WHERE sid = :sid AND spass = :spass LIMIT 1";
          $statement = $this->pdo->prepare($sql);
          $statement->bindParam(':now', $this->now, PDO::PARAM_INT);
          $statement->bindParam(':sid', $_SESSION[$this->route->role]['sid'], PDO::PARAM_STR);
          $statement->bindParam(':spass', $_SESSION[$this->route->role]['spass'], PDO::PARAM_STR);
          return $statement->execute();
        }
      } catch(\PDOException $e) { throw $e; }
    }

  }

}
