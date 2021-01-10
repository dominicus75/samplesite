<?php
/*
 * @package Templater
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Templater;

use \Dominicus75\Config\Config;

class Layout extends Component {

  /**
   *
   * @var string Fully qualified path name of template directory
   *
   */
  protected string $templateDirectory;

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
   * @var array buffer for not rendered component and variables
   *
   */
  protected array $buffer = [];


  /**
   * Constructor of class Skeleton.
   *
   * @param Config $config an isntace of \Dominicus75\Config\Config (extends \ArrayAccess)
   *
   * $config->offsetGet('variables') retrieves variables belongs to this view, as array
   * in below form (or null, if there is not variables in config file):
   * $variables => [
   *   '{{site_name}}' => 'It's my Site!',
   *   '{{lang}}' => 'en',
   *   '{{url}}' => $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
   * ];
   * the value must not contains markers!
   *
   * $config->offsetGet('components') retrieves components belongs to this view,
   * as multidimensional array, in below form (or nul, if there is not components in config file):
   * for example a head component, with file source:
   * $components  => [
   *   '%%head%%' => [
   *     'source' => ['file' => 'head.tpl'],
   *     'sources' => [
   *       '@@common@@' => '/fully/qualified/path/to/common.css',
   *       '@@desktop-common@@' => '/fully/qualified/path/to/desktop/common.css',
   *       '@@laptop-common@@' => '/fully/qualified/path/to/laptop/common.css',
   *       '@@tablet-common@@' => '/fully/qualified/path/to/table/common.css',
   *       '@@mobile-common@@' => '/fully/qualified/path/to/mobile/common.css'
   *     ],
   *     'variables' => []
   * ];
   *
   * And an aside component without source, if we don't want aside in this view
   * $components  => [
   *   '%%aside%%' => [
   *     'source' => [],
   *     'sources' => [],
   *     'variables' => []
   * ];
   *
   * And finally, a footer component, with text source
   * $components  => [
   *   '%%footer%%' => [
   *     'source' => ['text' => '<footer><p>It is a footer!</p></footer>'],
   *     'sources' => [],
   *     'variables' => []
   * ];
   *
   * @param string $cssDirectory Fully qualified path name of css root e. g. '/theme/default/css/'
   * @param string $templateDirectory Fully qualified path name of tpl root e. g. '/theme/default/'
   * @param string $skeletonFile Name of skeleton source file, for example 'skeleton.html'
   *
   * @return self
   *
   * @throws \Dominicus75\Templater\Exceptions\DirectoryNotFoundException
   * if given $config->offsetGet('tpl_directory') does not exists
   * @throws \Dominicus75\Templater\Exceptions\FileNotFoundException
   * if $config->offsetGet('skeleton') skeleton file does not exists
   *
   */
  public function __construct(
    Config $config,
    string $templateDirectory = '',
    string $skeletonFile = ''
  ){

    if(!empty($templateDirectory)) {
      if(is_dir($templateDirectory)) {
        $this->templateDirectory = $templateDirectory;
      } else {
        throw new Exceptions\DirectoryNotFoundException($templateDirectory.' does not exists.');
      }
    } else { $this->templateDirectory = Templater::DIR; }

    if(empty($skeletonFile)) { $skeletonFile = 'skeleton.html'; }

    try {
      parent::__construct($this->templateDirectory.$skeletonFile);
      $variables  = $config->offsetGet('variables');
      if(isset($variables)) { $this->updateVariables($variables); }
      $components = $config->offsetGet('components');
      if(isset($components)) { $this->buffer = $components; }
      $this->initComponents();
    } catch(Exceptions\DirectoryNotFoundException | Exceptions\FileNotFoundException $e) { throw $e; }

  }


  protected function initComponents(): self {
    if(preg_match_all(Templater::MARKERS['component'], $this->source, $matches)) {
      foreach($matches[0] as $marker){
        if(!array_key_exists($marker, $this->components)) { $this->components[$marker] = false; }
      }
    }
    return $this;
  }

  private function assign(array $arguments): self {

    if(!$this->hasMarker($arguments['marker'])) {
      throw new Exceptions\MarkerNotFoundException($arguments['marker'].' is not found in this source');
    } elseif($this->components[$arguments['marker']]) {
      throw new Exceptions\ComponentExistsException($arguments['marker'].' is already set');
    }

    switch(count($arguments)) {
      case 1:
        $this->source = str_replace($arguments['marker'], '', $this->source);
      break;
      case 2:
        try {
          if(isset($arguments['source']['file'])) {
            $component = new Source($this->templateDirectory.$arguments['source']['file']);
            $this->source = str_replace($arguments['marker'], $component->getSource(), $this->source);
          } elseif(isset($arguments['source']['text'])) {
            $this->source = str_replace($arguments['marker'], $arguments['source']['text'], $this->source);
          }
          $this->initSources();
          $this->initVariables();
        } catch(Exceptions\FileNotFoundException $e) { throw $e; }
      break;
      case 3:
        try {
          if(isset($arguments['source']['file'])) {
            $component = new Component($this->templateDirectory.$arguments['source']['file']);
            $component->setSources($arguments['sources']);
            if($component->isRenderable()) { $component->render(); }
            $this->source = str_replace($arguments['marker'], $component->getSource(), $this->source);
            $this->initSources();
            $this->initVariables();
          }
        } catch(Exceptions\FileNotFoundException $e) { throw $e; }
      break;
      case 4:
        try {
          if(isset($arguments['source']['file'])) {
            $component = new Component($this->templateDirectory.$arguments['source']['file']);
            $component->setSources($arguments['sources']);
            $component->setVariables($arguments['variables']);
            if($component->isRenderable()) { $component->render(); }
            $this->source = str_replace($arguments['marker'], $component->getSource(), $this->source);
            $this->initSources();
            $this->initVariables();
          }
        } catch(Exceptions\FileNotFoundException $e) { throw $e; }
      break;
    }

    $this->components[$arguments['marker']] = true;
    return $this;

  }

  /**
   *
   * @param string $marker in form '%%marker%%' (Component marker)
   * @param array $source this array must has only single element
   * this element's name must be 'file' (it is name of template file e. g. 'nav.tpl'),
   * or (xor!) 'text' (any text, typically a rendered component)
   * @see function assign switch statement, case 2.
   * @param array $sources The sources belongs to this Component
   * in [(string)'@@marker@@' => (string)'source'] form (source marker)
   * 'source' is fully qualified path name of source file
   * @param array $variables The string variables belongs to this Component
   * in (string)'{{marker}}' => (string)'value' form (variable marker).
   * Value must not contains markers!
   *
   * @retrun self
   *
   * @throws Dominicus75\Templater\Exceptions\FileNotFoundException if $sourceFile does not exists
   * @throws Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @throws Dominicus75\Templater\Exceptions\ComponentExistsException if component is already set
   *
   */
  public function assignComponent(
    string $marker,
    array $source = [],
    array $sources = [],
    array $variables = []
  ): self {

    $arguments['marker'] = $marker;
    if(!empty($source))     { $arguments['source']     = $source; }
    if(!empty($sources))    { $arguments['sources']    = $sources; }
    if(!empty($variables))  { $arguments['variables']  = $variables; }
    try {
      return $this->assign($arguments);
    } catch(Exceptions\MarkerNotFoundException |
            Exceptions\ComponentExistsException |
            Exceptions\FileNotFoundException $e) { throw $e; }

  }


  /**
   *
   * This method extracts component markers from source
   * and update components array
   *
   * @param array $components components belongs to this Skeleton
   * as multidimensional array, in below form:
   * for example a head component, with file source, and css sources:
   * $components  => [
   *   '%%head%%' => [
   *     'source' => ['file' => 'head.tpl'],
   *     'sources' => [
   *       '@@common@@' => '/fully/qualified/path/to/common.css',
   *       '@@desktop-common@@' => '/fully/qualified/path/to/desktop/common.css',
   *       '@@laptop-common@@' => '/fully/qualified/path/to/laptop/common.css',
   *       '@@tablet-common@@' => '/fully/qualified/path/to/table/common.css',
   *       '@@mobile-common@@' => '/fully/qualified/path/to/mobile/common.css'
   *     ],
   *     'variables' => []
   * ];
   *
   * And an aside component without source, if we don't want aside in this view
   * $components  => [
   *   '%%aside%%' => [
   *     'source' => [],
   *     'sources' => [],
   *     'variables' => []
   * ];
   *
   * And finally, a footer component, with text source
   * $components  => [
   *   '%%footer%%' => [
   *     'source' => ['text' => '<footer><p>It is a footer!</p></footer>'],
   *     'sources' => [],
   *     'variables' => []
   * ];
   *
   * @return self
   *
   * @throws Dominicus75\Templater\Exceptions\FileNotFoundException if $sourceFile does not exists
   * @throws Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @throws Dominicus75\Templater\Exceptions\ComponentExistsException if component is already set
   *
   */
  public function updateComponents(array $components = []): self {
    if(empty($components)) {
      $this->initComponents();
    } else {
      foreach($components as $marker => $component) {
        try {
          $this->assignComponent(
            $marker,
            $component['source'],
            $component['sources'],
            $component['variables']
          );
        } catch(Exceptions\MarkerNotFoundException |
                Exceptions\ComponentExistsException |
                Exceptions\FileNotFoundException $e) {
          throw $e;
        }
      }
    }
    return $this;
  }


  /**
   *
   * @param string $marker in form '%%marker%%' (Component marker)
   * @return bool
   *
   */
  protected function bufferHasComponent(string $marker): bool {
    return array_key_exists($marker, $this->buffer);
  }

  /**
   *
   * @param string $marker in form '%%marker%%' (Component marker)
   * @param string $parameter an array index
   * @param array $values what to be adding (only sources or variables)
   *
   * - sources:
   * $values = [
   *   '@@marker@@' => '/fully/qualified/path/to/source.tpl',
   *   '@@other-marker@@' => '/fully/qualified/path/to/desktop/source.css',
   *   '@@another-marker@@' => '/fully/qualified/path/to/laptop/source.tpl'
   * ];
   *
   * - variables:
   * $values = [
   *   '{{marker}}' => 'some text',
   *   '{{other-marker}}' => 'other text',
   *   '{{another-marker}}' => 'more text'
   * ];
   *
   * @return self
   *
   */
  protected function updateBufferedComponent(string $marker, string $parameter, array $values): self {
    $this->buffer[$marker][$parameter] = array_merge($this->buffer[$marker][$parameter], $values);
    return $this;
  }

  /**
   *
   * @param array $components components belongs to this view
   * as multidimensional array, in below form:
   * for example a head component, with file source, and css sources:
   * $components  => [
   *   '%%head%%' => [
   *     'source' => ['file' => 'head.tpl'],
   *     'sources' => [
   *       '@@common@@' => '/fully/qualified/path/to/common.css',
   *       '@@desktop-common@@' => '/fully/qualified/path/to/desktop/common.css',
   *       '@@laptop-common@@' => '/fully/qualified/path/to/laptop/common.css',
   *       '@@tablet-common@@' => '/fully/qualified/path/to/table/common.css',
   *       '@@mobile-common@@' => '/fully/qualified/path/to/mobile/common.css'
   *     ],
   *     'variables' => []
   * ];
   *
   * And an aside component without source, if we don't want aside in this view
   * $components  => [
   *   '%%aside%%' => [
   *     'source' => [],
   *     'sources' => [],
   *     'variables' => []
   * ];
   *
   * And finally, a footer component, with text source
   * $components  => [
   *   '%%footer%%' => [
   *     'source' => ['text' => '<footer><p>It is a footer!</p></footer>'],
   *     'sources' => [],
   *     'variables' => []
   * ];
   *
   * @return self
   *
   * @throws Dominicus75\Templater\Exceptions\FileNotFoundException if $sourceFile does not exists
   * @throws Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @throws Dominicus75\Templater\Exceptions\ComponentExistsException if component is already set
   *
   */
  public function updateComponentBuffer(array $components): self {
    $this->buffer = array_merge_recursive($this->buffer, $components);
    return $this;
  }

  /**
   *
   * @param string $marker in form '%%marker%%' (Component marker)
   * @return self
   *
   * @throws Dominicus75\Templater\Exceptions\FileNotFoundException if $sourceFile does not exists
   * @throws Dominicus75\Templater\Exceptions\MarkerNotFoundException if marker is not found
   * @throws Dominicus75\Templater\Exceptions\ComponentExistsException if component is already set
   *
   */
  public function assignBufferedComponent(string $marker): self {
    if($this->bufferHasComponent($marker)) {
      try {
        $this->assignComponent(
          $marker,
          $this->buffer[$marker]['source'],
          $this->buffer[$marker]['sources'],
          $this->buffer[$marker]['variables']
        );
        unset($this->buffer[$marker]);
        return $this;
      } catch(Exceptions\MarkerNotFoundException |
              Exceptions\ComponentExistsException |
              Exceptions\FileNotFoundException $e) {
        throw $e;
      }
    }
  }

  /**
   *
   * @param void
   * @return bool
   *
   */
  public function isRenderable(): bool {
    if(parent::isRenderable() && empty($this->buffer)) {
      return !in_array(false, $this->components, true);
    }
    return false;
  }

}
