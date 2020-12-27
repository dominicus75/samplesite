<?php
/*
 * @file Route.php
 * @package Core
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Core\Router;

class Route
{

  /**
   *
   * @var string name of called controller (e. g. 'Application\Controller\Page')
   *
   */
  private string $controller;

  /**
   *
   * @var string name of called action (e. g. 'read', 'update', etc.)
   *
   */
  private string $action;

  /**
   *
   * @var string category identifier (e. g. 'news' or 'news/abroad', etc)
   *
   */
  private ?string $category;

  /**
   *
   * @var string content identifier (e. g. 'index' or 'aboutus', etc)
   *
   */
  private string $cid;

  /**
   * Constructor of class Route.
   *
   * @return void
   */
  public function __construct(
    string $controller,
    string $action,
    string $cid,
    ?string $category = null
  ){

    $this->controller = $controller;
    $this->action     = $action;
    $this->category   = $category;
    $this->cid        = $cid;

  }

  public function __get($name): ?string { return $this->$name; }

}
