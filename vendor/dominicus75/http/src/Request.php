<?php
/*
 *
 * @package Http
 * @author Domokos Endre János <domokos.endrejanos@gmail.com>
 * @copyright 2020 Domokos Endre János
 * @license GNU General Public License v3 (https://opensource.org/licenses/GPL-3.0)
 *
 *
 */

namespace Dominicus75\Http;

use \Dominicus75\Validator\{Input, Pattern};


class Request
{

  use MessageTrait;

  protected string $uri;
  protected array $query = [];
  protected array $files = [];
  protected array $body  = [];

  public function __construct(
    $get = null,
    $post = null,
    $files = null
  ) {

    $this->uri = Uri::getPath();

    try {

      $this->setHeaders(apache_request_headers());

      if(!is_null($post)) {

        foreach($post as $key => $value) {
          $value = urldecode($value);
          $key   = Input::sanitizeHtml($key, null);
          $value = Input::sanitizeHtml($value, Pattern::ALLOWED_TAGS);
          $this->body[$key] = $value;
        }

      }


      if(!is_null($get)) {

        foreach($get as $key => $value) {
          $key   = Input::sanitizeHtml($key, null);
          $value = Input::sanitizeHtml($value, null);
          $this->query[$key] = $value;
        }

      }

      if(!is_null($files)) {
        try {
          $this->files = UploadedFileFactory::createFromGlobals($files);
        } catch(\InvalidArgumentException $e){ throw $e; }
      }


    } catch(\InvalidArgumentException $e) { throw $e; }

  }


  public function isXmlHttpRequest(): bool {
    return array_key_exists("X-Requested-With", $this->headers);
  }


  public function getMethod():string {
    return preg_match("/^(get|post)$/i", $_SERVER['REQUEST_METHOD'])
                      ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET' ;
  }

  public function getUri(): string { return $this->uri; }

  public function getUploadedFiles(): array { return $this->files; }

  public function getParsedBody(): array { return $this->body; }

  public function getQueryParams(): array { return $this->query; }

}
