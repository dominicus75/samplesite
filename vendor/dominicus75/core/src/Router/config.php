<?php

/*
 * @package Core
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

return [
  'roles' => ['visitor', 'user', 'admin'],
  'controllers' => [
    'ajax'      => '\Application\Controller\AJAX',
    'article'   => '\Application\Controller\Article',
    'category'  => '\Application\Controller\Category',
    'message'   => '\Application\Controller\Message',
    'user'      => '\Application\Controller\User',
    'admin'     => '\Application\Controller\Admin',
    'profile'   => '\Application\Controller\Profile',
    'dashboard' => '\Application\Controller\Dashboard',
    'page'      => '\Application\Controller\Page'
  ],
  'methods' => [
    'visitor' => [
      'ajax'      => ['get', 'post'],
      'article'   => ['view'],
      'category'  => ['view'],
      'message'   => ['view'],
      'page'      => ['view'],
      'user'      => ['register']
    ],
    'user'    => [
      'ajax'      => ['get', 'post'],
      'article'   => ['create', 'view', 'edit', 'delete'],
      'category'  => ['view'],
      'message'   => ['view'],
      'page'      => ['view'],
      'profile'   => ['view', 'edit', 'delete'],
      'user'      => ['login', 'logout', 'register']
    ],
    'admin'   => [
      'admin'     => ['login', 'logout'],
      'ajax'      => ['get', 'post'],
      'article'   => ['create', 'view', 'edit', 'delete'],
      'category'  => ['create', 'view', 'edit', 'delete'],
      'dashboard' => ['view'],
      'message'   => ['create', 'view', 'edit', 'delete'],
      'page'      => ['create', 'view', 'edit', 'delete'],
      'profile'   => ['create', 'view', 'edit', 'delete'],
      'user'      => ['create', 'view', 'edit', 'delete']
    ]
  ],
  'defaults' => [
    'role'       => 'visitor',
    'controller' => 'page',
    'method'     => 'view',
    'category'   => null,
    'content'    => '/'
  ]
];
