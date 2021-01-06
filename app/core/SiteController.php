<?php

/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


namespace Application\Core;

class SiteController extends AbstractController {

  /**
   *
   * @param Router\Route $route current route instance
   * @param array $parameters optional parameters of this controller
   *
   * @throws \Dominicus75\Config\NotFoundException
   * @throws \Dominicus75\Config\NotReadableException
   * @throws \Dominicus75\Config\NotWriteableException
   * @throws \PDOException
   *
   */
  protected function __construct(
    Router\Route $route,
    array $parameters = []
  ){

    parent::__construct($route, $parameters);

  }


}