<?php
/*
 * @file PageLayout.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\View;


class PageLayout extends \Dominicus75\MVC\AbstractLayout
{

  protected string $templateDir;
  protected string $skeletonFile;
  protected array $tplFiles;
  protected string $acion;

  public function __construct(string $action, $templateDir = '', $skeletonFile = '', $tplFiles = []) {

    if(empty($templateDir) && empty($skeletonFile) && empty($tplFiles)){
      parent::__construct(
        dirname(__DIR__).DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'templates',
        'skeleton.html',
        [
          '@@head.tpl@@' => 'head.tpl',
          '@@header.tpl@@' => 'header.tpl',
          '@@nav.tpl@@' => 'nav.tpl',
          '@@aside.tpl@@' => 'aside.tpl',
          '@@main.tpl@@' => 'page'.DIRECTORY_SEPARATOR.$action.'.tpl',
          '@@footer.tpl@@' => 'footer.tpl'
        ]
      );
    } else { parent::__construct($templateDir, $skeletonFile, $tplFiles); }

  }


}
