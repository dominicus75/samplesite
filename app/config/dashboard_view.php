<?php

/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


return [
  'tpl_directory' => ATPL,
  'skeleton' => 'admin.html',
  'variables' => [
    '{{site_name}}' => 'Vezérlőpult',
    '{{lang}}' => 'hu'
  ],
  'components' => [
    '%%head%%' => [
      'source' => ['file' => 'head.tpl'],
      'sources' => [
        '@@common@@' => ACSS.'common.css',
      ],
      'variables' => []
    ]
  ]
];