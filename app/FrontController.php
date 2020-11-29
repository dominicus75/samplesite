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
      try {
        $model = new Model\Page(new \Dominicus75\VariousTools\Config('pdo'), 'kapcsolat');


        $model->updateField("title", "Kapcsolat");
        /*$model->setContent([
          "pid" => "kapcsolat",
          "title" => "Kapcsolat",
          "keywords" => "kapcsolat, e-mai, írjál má nekünk",
          "description" => "Itt léphet kapcsolatba velünk",
          "body" => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi dapibus lacinia tincidunt. Aliquam vitae tortor gravida, consectetur tellus sed, rhoncus tellus. Fusce commodo faucibus augue nec pharetra. Phasellus pulvinar erat rutrum tellus venenatis, sit amet dapibus justo tempor. Pellentesque condimentum fermentum porttitor. Aenean ut neque turpis. Integer lectus turpis, dictum sed sodales ut, eleifend eu nisl. Proin molestie vel sapien vitae ultricies. Nam dignissim egestas erat, non imperdiet ante eleifend vitae. Cras auctor lorem id ipsum gravida, quis sodales quam maximus. Proin sed pretium velit. Maecenas euismod nunc non nulla fringilla, vel pharetra metus dictum. Aliquam nec sollicitudin lacus. Maecenas fringilla elementum mauris, aliquam aliquet quam. Vestibulum ornare purus vulputate, imperdiet diam mattis, semper neque. Pellentesque auctor est urna.</p>"
        ]);*/
        var_dump($model->update());


      } catch(\Dominicus75\MVC\InvalidFieldNameException $e) { echo $e->getMessage(); }
      //$controller = $router->dispatch();
      //$view = $controller->createView();

      echo "<pre>";
      var_dump($request);
      echo "<br><br>\n";
      var_dump($response);
      echo "<br><br>\n";
      var_dump($model);
      echo "<br><br>\n";
      echo "</pre>";


    } catch(\Dominicus75\Router\InvalidUriException $e) {
      $response->redirect("/error/404.html");
    } catch(\Dominicus75\Router\ControllerNotFoundException $e) {
      $response->redirect("/error/404.html");
    }

  }

}
