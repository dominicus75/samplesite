<?php
/*
 * @file UploadedFile.php
 * @package Http
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Http;

use \Dominicus75\Validator\Slug;

class UploadedFile implements \Psr\Http\Message\UploadedFileInterface
{

  const ERROR_MESSAGES = [
      UPLOAD_ERR_OK         => 'There is no error, the file uploaded with success',
      UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
      UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
      UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
      UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
      UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
      UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
      UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
  ];


  private string $name;
  private string $type;
  private string $tmp_name;
  private int $error;
  private int $size;
  private bool $moved = false;

  public function __construct(array $file){

    if(is_uploaded_file($file['tmp_name']) && $file['error'] == UPLOAD_ERR_OK){

      $info = pathinfo($file['name']);
      $slug = Slug::generate($info['filename']);

      $this->name = $slug.'.'.$info['extension'];
      $this->type = $file['type'];
      $this->tmp_name = $file['tmp_name'];
      $this->error = $file['error'];
      $this->size = $file['size'];

    } else {
      throw new \InvalidArgumentException(self::ERROR_MESSAGES[$file['error']]);
    }

  }


  public function getStream(): \Psr\Http\Message\StreamInterface {
    return new Stream(fopen($this->tmp_name), 'rw+');
  }


  public function moveTo(string $target){


    $this->moved = ('cli' === \PHP_SAPI)
                    ? rename($this->tmp_name, $target)
                    : move_uploaded_file($this->tmp_name, $target);

    if (false === $this->moved) {
      throw new \RuntimeException(
        sprintf('Uploaded file could not be moved to %s', $target)
      );
    }

  }


  public function getSize(): ?int {
    return $this->size;
  }


  public function getError(): ?int {
    return $this->error;
  }


  public function getClientFilename(): ?string {
    return $this->name;
  }


  public function getClientMediaType(): ?string {
    return $this->type;
  }

}
