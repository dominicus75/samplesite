<?php
/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Core;

use \Dominicus75\Model\PDO;
use \Dominicus75\Config\Config;

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
   * Constructor of class Authority.
   *
   * @param string $role user's role (admin|visitor)
   *
   * @return void
   */
  public function __construct(string $role)
  {
    try {
      $this->pdo = PDO::getInstance(new Config('mysql'));
      Session::init();
      Session::set('role', $role);
    } catch(\PDOException $e) { throw $e; }
  }


  public function authenticate(): bool {
    $this->now     = time();
    $this->expired = $this->now - 600;
    if(!isset($_SESSION[$_SESSION['role']]['spass']) && !isset($_SESSION[$_SESSION['role']]['sid'])) {
      return false;
    } else {
      $sql = "SELECT stime FROM sessions WHERE sid = :sid AND spass = :spass LIMIT 1";
      $statement = $this->pdo->prepare($sql);
      $statement->bindParam(':sid', $_SESSION[$_SESSION['role']]['sid'], PDO::PARAM_STR);
      $statement->bindParam(':spass', $_SESSION[$_SESSION['role']]['spass'], PDO::PARAM_STR);
      $statement->execute();
      $session = $statement->fetch(PDO::FETCH_ASSOC);
      if(!$session || $session['stime'] < $this->expired) {
        $sql = "DELETE FROM sessions WHERE sid = :sid AND spass = :spass LIMIT 1";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':sid', $_SESSION[$_SESSION['role']]['sid'], PDO::PARAM_STR);
        $statement->bindParam(':spass', $_SESSION[$_SESSION['role']]['spass'], PDO::PARAM_STR);
        $statement->execute();
        Session::destroy();
        return false;
      } else {
        $sql = "UPDATE sessions SET stime = :now WHERE sid = :sid AND spass = :spass LIMIT 1";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':now', $this->now, PDO::PARAM_INT);
        $statement->bindParam(':sid', $_SESSION[$_SESSION['role']]['sid'], PDO::PARAM_STR);
        $statement->bindParam(':spass', $_SESSION[$_SESSION['role']]['spass'], PDO::PARAM_STR);
        return $statement->execute();
      }
    }
  }

}
