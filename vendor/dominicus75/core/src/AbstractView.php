<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Core;

use \Dominicus75\Templater\Skeleton;

abstract class AbstractView
{

  /**
   *
   * @var array The renderable contents
   *
   */
  protected array $content;

  /**
   *
   * @var string action (e. g. 'create', 'delete', default: 'read')
   *
   */
  protected string $action;

  /**
   *
   * @var \Dominicus75\Templater\Skeleton view engine instance
   *
   */
  protected Skeleton $view;

  /**
   * Constructor of class AbstractView.
   *
   * @return void
   */
  protected function __construct(
    array $content,
    string $action,
    Skeleton $view
  ) {
    $this->content = $content;
    $this->action  = $action;
    $this->view    = $view;
  }


  /**
   *
   * @param void
   * @return string
   * @throws \RuntimeException if this view is not renderable
   *
   */
  public function render(): string {
    try {
      return $this->view->render();
    } catch(\RuntimeException $e) { throw $e; }
  }

}
