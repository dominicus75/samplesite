<?php
/*
 * @file TemplateIterator.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class TemplateIterator extends Template
{

  /**
   *
   * @var string Fully qualified path name of iterative template file (tpl)
   *
   */
  protected string $iterativeTemplateUrl;

  /**
   *
   * @var string in form '&&marker&&'
   *
   */
  protected string $marker;

  /**
   * @var string result of iteration
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
   * @param string $iterativeTemplateUrl Fully qualified path name of iterative template file (tpl)
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
    string $inclusiveTemplateUrl,
    string $iterativeTemplateUrl,
    string $marker,
    array $content
  ){

    try {

      parent::__construct($inclusiveTemplateUrl);

      if(preg_match(Templater::MARKERS['foreach'], $this->source, $matches)) {
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

      if(is_file($iterativeTemplateUrl)) {
        $this->iterativeTemplateUrl = $iterativeTemplateUrl;
      } else { throw new FileNotFoundException($iterativeTemplateUrl.' does not exists.'); }

      foreach($content as $item) {

        if(!is_array($item)) {
          throw new \InvalidArgumentException(
            'All items of this array must be an array'
          );
        }

        try {
          $iterativeTemplate = new IterativeTemplate($this->iterativeTemplateUrl);
          $iterativeTemplate->setVariables($item);
          $this->result .= $iterativeTemplate->render().PHP_EOL;
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
      throw new \RuntimeException('This TemplateIterator is not renderable yet.');
    }

  }


}
