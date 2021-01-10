<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;

use \Dominicus75\Config\Config;
use \Dominicus75\Model\PDO;

class Nav
{

  const HOME = ['{{url}}' => '/', '{{target}}' => 'Kezdőlap'];

  /**
   *
   * @var \Dominicus75\Model\PDO
   *
   */
  private PDO $pdo;

  /**
   *
   * @var array list of pages
   *
   */
  private array $pages;

  /**
   *
   * @var array list of categories
   *
   */
  private array $categories;


  /**
   * Constructor of class Nav.
   *
   * @return void
   */
  public function __construct()
  {
    $this->pdo = PDO::getInstance(new Config('mysql'));
    $this->setPages();
    $this->categories = $this->setCategories();
  }


  private function setPages(): self {

    if($this->pdo->hasTable('pages')) {

      $statement = $this->pdo->query("SELECT `url`, `title` FROM `pages`");

      if($statement->execute()) {
        if($pages = $statement->fetchAll()) {
          foreach($pages as $page) {
            $url = $page['url'] == '/' ? $page['url'] : $page['url'].'.html';
            $this->pages[$page['url']] = ['{{url}}' => $url, '{{target}}' => $page['title']];
          }
          return $this;
        } else {
          throw new \PDOException('PDOStatement::fetchAll() function returned with false');
        }
      } else {
        throw new \PDOException('PDOStatement::execute() function returned with false');
      }

    } else {
      throw new \PDOExpection('the specified pages table not found');
    }

  }


  private function setCategories(?string $parent = null): array {

    if($this->pdo->hasTable('categories')) {

      $sql = is_null($parent)
        ? "SELECT `url`, `title` FROM `categories` WHERE `parent` IS NULL"
        : "SELECT `url`, `title`, `parent` FROM `categories` WHERE `parent` = '".$parent."'";

      $statement = $this->pdo->query($sql);

      if($statement->execute()) {
        if($categories = $statement->fetchAll()) {
          foreach($categories as $category) {
            $result[$category['url']] = [
              'link' => ['{{url}}' => $category['url'], '{{target}}' => $category['title']],
              'child' => null
            ];
            $child = $this->setCategories($category['url']);
            if(!empty($child)) { $result[$category['url']]['child'] = $child; }
          }
          return $result;
        } else { return []; }
      } else { return []; }

    } else { throw new \PDOException('categories table does not exists in this table'); }

  }

  public function getPages(): array { return $this->pages; }
  public function getCategories(): array { return $this->categories; }
  public function getMenu(): array {
    return [
      'pages'      => $this->pages,
      'categories' => $this->categories
    ];
  }

}
