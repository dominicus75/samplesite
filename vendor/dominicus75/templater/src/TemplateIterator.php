<?php
/*
 * @file TemplateIterator.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class TemplateIterator
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
   * @param string $inclusiveTemplateUrl Fully qualified path name of template file (tpl)
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
  public function __construct(string $iterativeTemplateUrl, array $content){

      if(is_file($iterativeTemplateUrl)) {

        $this->iterativeTemplateUrl = $iterativeTemplateUrl;

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

      } else { throw new FileNotFoundException($iterativeTemplateUrl.' does not exists.'); }

  }


  /**
   *
   * @param void
   * @return string
   * @throws \RuntimeException if this TemplateIterator is not renderable
   *
   */
  public function render(): string {

    if($this->renderable) {
      return $this->result;
    } else {
      throw new \RuntimeException('This TemplateIterator is not renderable yet.');
    }

  }


}
