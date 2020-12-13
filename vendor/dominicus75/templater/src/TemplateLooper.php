<?php
/*
 * @file TemplateLooper.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class TemplateLooper extends Template
{

  /**
   *
   * @var string Fully qualified path name of iterative template file (tpl)
   *
   */
  protected string $itemTemplateUrl;

  /**
   *
   * @var string in form '&&marker&&'
   *
   */
  protected string $marker;

  /**
   * @var string result of foreach
   *
   */
  protected string $result = '';

  /**
   *
   * @var bool This template is rendereble or not
   *
   */
  protected bool $renderable = false;

  /**
   *
   * @param string $outerTemplateUrl Fully qualified path name of template file (tpl)
   * @param string $itemTemplateUrl Fully qualified path name of iterative template file (tpl)
   * @param string $marker in form '&&marker&&'
   * @param array $content
   *
   * @throws \Dominicus75\Templater\FileNotFoundException if the Looper or Item
   * template file does not exists
   * @throws \InvalidArgumentException if foreach marker is invalid or missing
   * @throws \InvalidArgumentException if either $content item is not an array
   *
   */
  public function __construct(
    string $outerTemplateUrl,
    string $itemTemplateUrl,
    string $marker,
    array $content
  ){

    try {

      parent::__construct($outerTemplateUrl);

      if(preg_match(Skeleton::MARKERS['foreach'], $this->source, $matches)) {
        if($matches[0] == $marker) {
          $this->marker = $marker;
        } else {
          throw new \InvalidArgumentException('Invalid forech marker');
        }
      } else {
        throw new \InvalidArgumentException(
          'No forech marker found in this template file'
        );
      }

      if(is_file($itemTemplateUrl)) {
        $this->itemTemplateUrl = $itemTemplateUrl;
      } else { throw new FileNotFoundException($itemTemplateUrl.' does not exists.'); }

      foreach($content as $item) {

        if(!is_array($item)) {
          throw new \InvalidArgumentException(
            'All items of this array must be an array'
          );
        }

        try {
          $itemTemplate = new ItemTemplate($this->itemTemplateUrl);
          $itemTemplate->setVariables($item);
          $this->result .= $itemTemplate->render().PHP_EOL;
        } catch(\InvalidArgumentException |
                \RuntimeException |
                \FileNotFoundException $e) { throw $e; }

      }

      $this->renderable = true;


    } catch(FileNotFoundException $e) { throw $e; }

  }


  /**
   *
   * @param void
   * @return string
   * @throws \RuntimeException if this Looper is not renderable
   *
   */
  public function render(): string {

    if($this->renderable) {

      return str_replace(
        $this->marker,
        $this->result,
        $this->source
      );

    } else {
      throw new \RuntimeException('This TemplateLooper is not renderable yet.');
    }

  }


}
