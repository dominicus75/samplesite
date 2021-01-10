<?php

/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


return [
  'tpl_directory' => TPL,
  'skeleton' => 'site.html',
  'variables' => [
    '{{site_name}}' => 'Globetrotter',
    '{{lang}}' => 'hu',
    '{{url}}' => $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
  ],
  'components' => [
    '%%head%%' => [
      'source' => ['file' => 'head.tpl'],
      'sources' => [
        '@@common@@' => CSS.'common.css',
        '@@desktop-common@@' => CSS.'desktop'.DSR.'common.css',
        '@@laptop-common@@' => CSS.'laptop'.DSR.'common.css',
        '@@tablet-common@@' => CSS.'tablet'.DSR.'common.css',
        '@@mobile-common@@' => CSS.'mobile'.DSR.'common.css'
      ],
      'variables' => []
    ],
    '%%header%%' => [
      'source' => ['file' => 'header.tpl'],
      'sources' => [],
      'variables' => []
    ]
  ]
];