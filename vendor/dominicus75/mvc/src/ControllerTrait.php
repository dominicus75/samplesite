<?php
/*
 *
 * @package Mvc
 * @author Domokos Endre János <domokos.endrejanos@gmail.com>
 * @copyright 2020 Domokos Endre János
 * @license GNU General Public License v3 (https://opensource.org/licenses/GPL-3.0)
 *
 *
 */

namespace Dominicus75\Mvc;

use \Dominicus75\Validator\{Pattern as Pattern, Slug as Slug, Input as Input};

trait ControllerTrait
{

  protected $name;
  protected $method;
  protected $action;
  protected $uri;

  protected $meta;
  protected $images;
  protected $body;

  public function setTitle(string $title):void {

    if(Input::validatePlainText($title, 5, 70)) {
      $this->meta['title'] = $title;
    }

  }

  public function setDescription(string $description):void {

    if(Input::validatePlainText($description, 5, 170)) {
      $this->meta['description'] = $description;
    }

  }

  public function setImage($id, string $url):bool {

    if(!preg_match("/^(primary|[0-9]{1,2})$/", $id)) { return false; }

    if(file_exists($_SERVER['DOCUMENT_ROOT']."upload/images".$url)){
      $this->images[$id] = $url;
      return true;
    } else { return false; }

  }

  public function createView() {

    return new \Application\View\Template(
                $this->name,
                $this->method,
                $this->action,
                [$this->meta, $this->images, $this->body]);

  }

}
