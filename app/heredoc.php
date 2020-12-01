<?php

return <<<EOT
<!DOCTYPE html>
<html lang="hu">
  <head>
    <title>:{title}: | Globetrotter</title>
    <meta charset="utf-8" />
    <meta name="description" content=":{description}:">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" type="text/css" href="/css/common.css" />
    <link rel="stylesheet" type="text/css" media="screen and (min-width: 1281px)" href="/css/desktop.css" />
    <link rel="stylesheet" type="text/css" media="screen and (min-width: 1025px) and (max-width: 1280px)" href="/css/laptop.css" />
    <link rel="stylesheet" type="text/css" media="screen and (min-width: 768px) and (max-width: 1024px)" href="/css/tablet.css" />
    <link rel="stylesheet" type="text/css" media="screen and (max-width: 767px)" href="/css/mobile.css" />
  </head>
  <body>

  <header>
    <h1><a href="/"><i class="fa fa-globe"></i>&nbsp;Globetrotter</a></h1>
    <p class="textCenter site_slogan">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
  </header>

    :{nav}:

    :{main}:

  <footer>
    <p>&copy; 2020 <a href="mailto:domokos.endrejanos@gmail.com">Domokos Endre János</a> (BC2K6G)</p>
  </footer>

  </body>
</html>
EOT;
