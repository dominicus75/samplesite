<?php

/*
 * @file router.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

use \Dominicus75\Core\Router\Route as Route;

return [
  'index.html' => new Route(
    '\Application\Controller\Page',
    'read',
    'index'
  ),
  'edit/index.html' => new Route(
    '\Application\Controller\Page',
    'edit',
    'index'
  ),
  'create/index.html' => new Route(
    '\Application\Controller\Page',
    'create',
    'index'
  ),
  'delete/index.html' => new Route(
    '\Application\Controller\Page',
    'delete',
    'index'
  )
];

