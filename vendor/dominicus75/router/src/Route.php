<?php
/*
 * @package Router
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Router;

class Route
{

  /**
   *
   * @var string name of role (visitor|user|admin)
   * - visitor: an unregistered user, visitor
   * - user: a registered user without admin permissions
   * - admin: a registered user with admin permissions
   *
   */
  private string $role;

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
  private string $method;

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
  private ?string $cid;

  /**
   * Constructor of class Route.
   *
   * @return void
   */
  public function __construct(
    string $role,
    string $controller,
    string $method,
    ?string $cid,
    ?string $category = null
  ){

    $this->role       = $role;
    $this->controller = $controller;
    $this->method     = $method;
    $this->category   = $category;
    $this->cid        = $cid;

  }

  public function __get($name): ?string { return $this->$name; }

}
