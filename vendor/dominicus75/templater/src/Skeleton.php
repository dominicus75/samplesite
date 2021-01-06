<?php
/*
 * @file Skeleton.php
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;


class Skeleton extends Component {

  /**
   *
   * @var string Fully qualified path name of css directory
   *
   */
  private string $cssDirectory;

  /**
   *
   * @var array The components belongs to this skeleton
   * in (string)'%%marker%%' => (bool)false|true form
   * It is true, if component has assigned, false otherwise
   *
   */
  protected array $components = [];


  /**
   *
   * @param string $cssDirectory Fully qualified path name of css root e. g. '/theme/default/css/'
   * @param string $templateDirectory Fully qualified path name of tpl root e. g. '/theme/default/'
   * @param string $skeletonFile Name of skeleton source file, for example 'skeleton.html'
   *
   * @throws \Dominicus75\Templater\Exceptions\DirectoryNotFoundException
   * if given directory does not exists
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException
   * if given skeleton file does not exists
   *
   */
  public function __construct(
    string $cssDirectory,
    string $templateDirectory = '',
    string $skeletonFile = ''
  ){

    if(is_dir($cssDirectory)) {

      $this->cssDirectory = $cssDirectory;

      if(empty($skeletonFile)) { $skeletonFile = 'skeleton.html'; }

      try {
        parent::__construct($skeletonFile, $templateDirectory);
        $this->updateComponents();
      } catch(Exceptions\DirectoryNotFoundException | Exceptions\FileNotFoundException $e) { throw $e; }

    } else {
      throw new Exceptions\DirectoryNotFoundException($cssDir.' does not exists.');
    }

  }


  /**
   *
   * This method extracts component markers from source
   * and update components array
   *
   * @param void
   * @return void
   *
   */
  private function updateComponents(): self {
    if(preg_match_all(Templater::MARKERS['component'], $this->source, $matches)) {
      foreach($matches[0] as $marker){ $this->components[$marker] = false; }
    }
    return $this;
  }


  /**
   *
   * @parem string $marker in form '%%marker%%'
   * @param string $templateDirectory Fully qualified path name of tpl root e. g. '/theme/default/'
   * @param string $sourceFile name of template file (e. g. 'nav.tpl')
   * @throws Dominicus75\Templater\Exceptions\FileNotFoundException if $sourceFile does not exists
   * @throws Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   *
   */
  public function assignComponent(
    string $marker,
    string $templateDirectory,
    string $sourceFile = ''
  ): self {

    if($this->hasMarker($marker)){

      if(empty($sourceFile)) {
        $this->source = str_replace($marker, '', $this->source);
      } else {
        $sourceFile = empty($templateDirectory)
          ? $this->templateDirectory.$sourceFile
          : $templateDirectory.$sourceFile;
        try {
          $source = new Source($sourceFile);
          $this->source = str_replace($marker, $source->getSource(), $this->source);
          $this->updateSources();
          $this->updateVariables();
        } catch(Exceptions\FileNotFoundException $e) { throw $e; }
      }

      $this->components[$marker] = true;
      return $this;

    } else {
      throw new Exceptions\MarkerNotFoundException($marker.' is not found in this source');
    }

  }


  /**
   *
   * @parem string $marker in form '@@marker@@'
   * @param string $cssFile name of template (tpl) file, for example 'common.css'
   * @throws Exceptions\MarkerNotFoundException if marker not found
   * @throws Exceptions\FileNotFoundException if css file not found
   *
   */
  public function assignCSS(string $marker, string $cssFile = ''): self {

    if($this->hasMarker($marker)){

      if(empty($cssFile)) {
        $this->source = str_replace($marker, '', $this->source);
      } else {
        try {
          $css = new Source($this->cssDirectory.$cssFile);
          $this->source = str_replace($marker, $css->getSource(), $this->source);
          $this->updateSources();
          $this->updateVariables();
        } catch(Exceptions\FileNotFoundException $e) { throw $e; }
      }

      $this->sources[$marker] = true;
      return $this;

    } else {
      throw new Exceptions\MarkerNotFoundException($marker.' is not found in this source');
    }

  }


  /**
   *
   * @param string $type the content type (page, article, category, etc.)
   * @param string $templateDirectory Fully qualified path name of tpl root e. g. '/theme/default/'
   * @param bool $socialMedia if true, Templater inserts to head the social media
   * meta tags
   *
   * @return self
   *
   */
  public function insertHead(
    string $type,
    string $templateDirectory = '',
    bool $socialMedia = true
  ): self {

    $this->assignComponent('%%head%%', $templateDirectory, 'head.tpl');
    if($socialMedia) {
      $this->assignSource('@@meta@@', $templateDirectory, 'social_meta.tpl');
    } else {
      $this->assignSource('@@meta@@', $templateDirectory, '');
    }
    $this->assignCSS('@@common@@', 'common.css');
    $this->assignCSS('@@desktop-common@@', 'desktop'.DIRECTORY_SEPARATOR.'common.css');
    $this->assignCSS('@@desktop-typified@@', 'desktop'.DIRECTORY_SEPARATOR.$type.'.css');
    $this->assignCSS('@@laptop-common@@', 'laptop'.DIRECTORY_SEPARATOR.'common.css');
    $this->assignCSS('@@laptop-typified@@', 'laptop'.DIRECTORY_SEPARATOR.$type.'.css');
    $this->assignCSS('@@tablet-common@@', 'tablet'.DIRECTORY_SEPARATOR.'common.css');
    $this->assignCSS('@@tablet-typified@@', 'tablet'.DIRECTORY_SEPARATOR.$type.'.css');
    $this->assignCSS('@@mobile-common@@', 'mobile'.DIRECTORY_SEPARATOR.'common.css');
    $this->assignCSS('@@mobile-typified@@', 'mobile'.DIRECTORY_SEPARATOR.$type.'.css');
    return $this;

  }


  /**
   * It insert a navigation element to this code
   *
   * @param string $templateDirectory Fully qualified path name of tpl root e. g. '/theme/default/'
   *
   * @return self
   *
   */
  public function insertNav(
    array $menu,
    string $templateDirectory
  ): self {

    try {
      $nav = new Nav($menu, '');
      $this->source = str_replace('%%nav%%', $nav->getSource(), $this->source);
      $this->updateSources();
      $this->updateVariables();
      $this->components['%%nav%%'] = true;
      return $this;
    } catch(Exceptions\DirectoryNotFoundException | Exceptions\FileNotFoundException $e) { throw $e; }


  }
















  /**
   *
   * @param void
   * @return bool
   *
   */
  public function isRenderable(): bool {

    if(parent::isRenderable() && $this->isComplete()) {
      return in_array(false, $this->components, true);
    }
    return false;

  }


}
