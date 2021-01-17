<?php

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
    'page'      => '\Application\Controller\Page'
  ],
  'methods' => [
    'ajax'      => ['get', 'post'],
    'article'   => ['create', 'view', 'edit', 'delete'],
    'category'  => ['create', 'view', 'edit', 'delete'],
    'message'   => ['create', 'view', 'edit', 'delete'],
    'user'      => ['login', 'logout', 'register', 'confirm'],
    'admin'     => ['login', 'logout', 'dashboard'],
    'profile'   => ['create', 'view', 'edit', 'delete'],
    'page'      => ['create', 'view', 'edit', 'delete']
  ],
  'enabled' => [
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
      'article'   => ['view'],
      'category'  => ['view'],
      'message'   => ['view'],
      'page'      => ['view'],
      'profile'   => ['view', 'edit', 'delete'],
      'user'      => ['login', 'logout', 'register', 'confirm']
    ],
    'admin'   => [
      'admin'     => ['login', 'logout', 'dashboard'],
      'ajax'      => ['get', 'post'],
      'article'   => ['create', 'view', 'edit', 'delete'],
      'category'  => ['create', 'view', 'edit', 'delete'],
      'message'   => ['create', 'view', 'edit', 'delete'],
      'page'      => ['create', 'view', 'edit', 'delete'],
      'profile'   => ['create', 'view', 'edit', 'delete']
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
