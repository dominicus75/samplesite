<?php
/*
 * @file UploadedFileFactory.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Http;

class UploadedFileFactory
{

  /**
   * Slim Framework (https://slimframework.com) UploadedFile
   * osztályából nyúlva
   *
   */
  public static function createFromGlobals(array $files = []): array {

    $parsed = [];
    foreach ($files as $field => $file) {
      $parsed[$field] = [];
      if (!is_array($file['error'])) {
        try {
          $parsed[$field] = new UploadedFile($file);
        } catch(InvalidArgumentException $e) { throw $e; }
      } else {
        $subArray = [];
        foreach ($file['error'] as $fileIndex => $error) {
          $subArray[$fileIndex]['name']     = $file['name'][$fileIndex];
          $subArray[$fileIndex]['type']     = $file['type'][$fileIndex];
          $subArray[$fileIndex]['tmp_name'] = $file['tmp_name'][$fileIndex];
          $subArray[$fileIndex]['error']    = $file['error'][$fileIndex];
          $subArray[$fileIndex]['size']     = $file['size'][$fileIndex];
          try {
            $parsed[$field] = static::createFromGlobals($subArray);
          } catch(InvalidArgumentException $e) { throw $e; }
        }
      }
    }

    return $parsed;

  }

}
