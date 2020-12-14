<?php
/*
 * @file FrontController.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application;

use \Dominicus75\Http\{Request, Response, Uri};
use \Dominicus75\Router\Router as Router;

class FrontController
{


  public static function run() {

    $get   = (count($_GET) > 0) ? $_GET : null;
    $post  = (count($_POST) > 0) ? $_POST : null;
    $files = (count($_FILES) > 0) ? $_FILES : null;

    try{

      $request  = new Request($get, $post, $files);
      $response = new Response();
      $router   = new Router($request);
      //$controller = $router->dispatch();

      $dom = new \Dominicus75\Templater\Skeleton(__DIR__.'/view/templates', 'skeleton.html');

      $dom->assignTemplateSource('@@head@@', 'head.tpl');
      $dom->assignTemplateSource('@@header@@', 'header.tpl');
      $dom->assignTemplateSource('@@nav@@', 'nav.tpl');
      $dom->assignTemplateSource('@@aside@@', 'aside.tpl');
      $dom->assignTemplateSource('@@main@@', 'page/read.tpl');
      $dom->assignTemplateSource('@@footer@@', 'footer.tpl');
      $dom->buildLayout();

      $dom->assignTemplateIterator(
        'navItem.tpl',
        '{{nav}}',
        [
          ['{{url}}' => '/', '{{target}}' => 'Kezdőlap'],
          ['{{url}}' => '/rolunk.html', '{{target}}' => 'Rólunk'],
          ['{{url}}' => '/kapcsolat.html', '{{target}}' => 'Kapcsolat'],
          ['{{url}}' => '/vendegkonyv.html', '{{target}}' => 'Vendégkönyv']
        ]
      );

      $aside = new \Application\Element\Aside('http://www.mnb.hu/arfolyamok.asmx?wsdl');


      $dom->setVariables([
        '{{title}}' => 'Kezdőlap',
        '{{description}}' => 'A Globetrotter utazási iroda oldala',
        '{{row}}' => $aside->renderView(),
        '{{body}}' => '<p class="textCenter site_slogan">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>'
      ]);
      echo $dom->render();



//echo $nav->render();
//$dom->render();
//var_dump($layout->isRenderable());
//echo "<pre>";
//var_dump($dom);
//echo "<br><br>\n";
//var_dump($controller);
//echo "<br><br>\n";
//echo "</pre>";



    } catch(\Dominicus75\Router\InvalidUriException | \Dominicus75\Router\ControllerNotFoundException $e) {
      $response->redirect("/error/404.html");
    }

  }

}
