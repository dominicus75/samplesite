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

  use ParameterizableTrait;

  /**
   *
   * @var \Dominicus75\Templater\Skeleton view engine instance
   *
   */
  protected Skeleton $view;

  /**
   *
   * @var array The renderable contents
   *
   */
  protected array $content;

  /**
   * Constructor of class AbstractView.
   *
   * @return void
   */
  protected function __construct(
    array $contentParameters,
    Skeleton $view,
    array $content = []
  ) {
    $this->setParameters($contentParameters);
    $this->view    = $view;
    $this->content = $content;
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
      $this->view->render();
      return $this->view->getSource();
    } catch(\RuntimeException $e) { throw $e; }
  }

}
