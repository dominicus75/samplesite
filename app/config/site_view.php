<?php

/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


return [
  'tpl_directory' => UTPL,
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
        '@@common@@' => UCSS.'common.css',
        '@@desktop-common@@' => UCSS.'desktop'.DSR.'common.css',
        '@@laptop-common@@' => UCSS.'laptop'.DSR.'common.css',
        '@@tablet-common@@' => UCSS.'tablet'.DSR.'common.css',
        '@@mobile-common@@' => UCSS.'mobile'.DSR.'common.css'
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