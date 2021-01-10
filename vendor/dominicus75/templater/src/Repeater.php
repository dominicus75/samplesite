<?php
/*
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Repeater extends Source
{

  /**
   *
   * @param string $inclusiveTemplateUrl Fully qualified path name of template file (tpl)
   * @param string $iterativeTemplateUrl Fully qualified path name of iterative template file (tpl)
   * @param string $marker in form '@@marker@@'
   * @param array $content every array item must be an array
   * in [(string)'{{marker}}' => (string)'value'] form
   *
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException if the
   * iterative template file does not exists
   * @throws \Dominicus75\Templater\Exceptions\MarkerNotFoundException if a marker is not found
   * @throws \Dominicus75\Templater\Exceptions\VariableExistsException if a marker has already value
   * @throws \Dominicus75\Templater\Exceptions\NotRenderableException if this source is not renderable yet
   *
   */
  public function __construct(string $iterativeTemplateUrl, array $content) {

    parent::__construct();

    if(is_file($iterativeTemplateUrl)) {

      foreach($content as $item) {

        if(!is_array($item)) {
          throw new \InvalidArgumentException(
            'All items of this array must be an array'
          );
        }

        try {
          $itemSource = new RenderableSource($iterativeTemplateUrl, $item);
          $this->source .= $itemSource->display().PHP_EOL;
        } catch(
          Exceptions\FileNotFoundException |
          Exceptions\MarkerNotFoundException |
          Exceptions\VariableExistsException |
          Exceptions\NotRenderableException $e) { throw $e; }

      }

    } else { throw new Exceptions\FileNotFoundException($iterativeTemplateUrl.' does not exists.'); }

  }

}
