<?php

/*
 * @package Router
 * @copyright 2021 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */


namespace Dominicus75\Router;

use \Dominicus75\Config\Config;

class Mapper extends Config {

  /**
   *
   * @param string $configFile name of config file
   * @param string $configDirectory fully qualified name of config directory
   * if it is empty, Mapper is going to use the default config directory
   *
   */
  public function __construct(string $configFile, string $configDirectory = '') {
    parent::__construct($configFile, $configDirectory);
  }


  /**
   * Save the specified Route instance to config file
   * @param string $requestUri the requested URI as array index
   * @param Route a Route instance, to save
   * @return void
   * @throws NotWriteableException, if config file not exists or is not writeable
   *
   */
  public function saveRouteToConfig(string $requestUri, Route $route): void {
    if(!$this->offsetExists($requestUri)) {
      $this->offsetSet(
        $requestUri,
        [
          'role'       => $route->role,
          'controller' => $route->controller,
          'method'     => $route->method,
          'category'   => $route->category,
          'content'    => $route->cid
        ]
      );
      try {
        $this->save();
      } catch(\Dominicus75\Config\NotWriteableException $e) { throw new NotWriteableException($e->getMessage()); }
    }
  }

}