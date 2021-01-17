<?php

/*
 * @package samplesite
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


return [
  'tpl_directory' => ETPL,
  'skeleton' => 'entrance.html',
  'variables' => [
    '{{site_name}}' => 'Globetrotter',
    '{{lang}}' => 'hu'
  ],
  'components' => [
    '%%head%%' => [
      'source' => ['file' => 'head.tpl'],
      'sources' => [
        '@@entrance@@' => ECSS.'entrance.css',
      ],
      'variables' => []
    ]
  ]
];