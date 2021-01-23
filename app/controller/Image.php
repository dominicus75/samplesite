<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Controller;

use \Dominicus75\Router\Route;
use \Dominicus75\Http\UploadedFile;
use \Dominicus75\Model\{
  PDO,
  Entity,
  ContentNotFoundException,
  InvalidFieldNameException,
  InvalidStatementException
};
use \Application\Core\{Authority, Session};

class Image
{

  const DIMENSIONS = [
    'avatar' => [
      'width'  => 250,
      'height' => 250,
      'ratio'  => 1
    ],
    'thumbnail' => [
      'width'  => 480,
      'height' => 270,
      'ratio'  => 1.78
    ],
    'preview' => [
      'width'  => 960,
      'height' => 540,
      'ratio'  => 1.78
    ],
    'large' => [
      'width'  => 1920,
      'height' => 1080,
      'ratio'  => 1.78
    ],
  ];

  const ALLOWED_MIMES  = ["png" => "image/png", "jpg" => "image/jpeg"];

  const ERRORS = [
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk',
    8 => 'A PHP extension stopped the file upload'
  ];

  /**
   *
   * @var string path of images tmp
   *
   */
  private string $tmp;

  /**
   *
   * @var string type (e. g. 'avatar')
   *
   */
  private string $type;

  /**
   *
   * @var string name The original name of the file on the client machine
   *
   */
  private string $name;

  /**
   *
   * @var string tmp_name The temporary filename of the file in which the uploaded
   * file was stored on the server
   *
   */
  private string $tmp_name;

  /**
   *
   * @var string mimetype The mime type of the image file
   *
   */
  private string $mimetype;

  /**
   *
   * @var string extension the image file extension ('.jpg' or '.png')
   *
   */
  private string $extension;

  /**
   *
   * @var int size The size, in bytes, of the uploaded file
   *
   */
  private int $size;

  /**
   *
   * @var int width of the image
   *
   */
  private int $width;

  /**
   *
   * @var int height of the image
   *
   */
  private int $height;

  /**
   *
   * @var float aspect ratio
   *
   */
  private float $ratio;

  /**
   *
   * @var int index current timestamp
   *
   */
  private int $index;

  /**
   *
   * @var \Dominicus75\Model\Entity
   *
   */
  private Entity $imageEntity;

  /**
   *
   * @param array $files
   *
   */
  public function __construct(array $files) {

    $this->type = key($files);

    if(!in_array($files[$this->type]->getClientMediaType(), self::ALLOWED_MIMES)) {
      throw new \Error($files[$this->type]->getClientMediaType().' is not allowed');
    } elseif($files[$this->type]->getError() > 0) {
      throw new \Error(self::ERRORS[$files[$this->type]->getError()]);
    } elseif(!is_uploaded_file($files[$this->type]->getTmpName())) {
      throw new \Error(self::ERRORS[UPLOAD_ERR_NO_FILE]);
    } else {
      $sizes = getimagesize($files[$this->type]->getTmpName());
      if(!$sizes) {
        throw new \Error($files[$this->type]->getTmpName().' is not an image file');
      } else {
        $this->tmp       = dirname(dirname(__DIR__)).'/public/upload/tmp/';
        $this->name      = $files[$this->type]->getClientFilename();
        $this->tmp_name  = $files[$this->type]->getTmpName();
        $this->extension = pathinfo($files[$this->type]->getClientFilename(), PATHINFO_EXTENSION);
        $this->size      = $files[$this->type]->getSize();
        $this->mimetype  = $files[$this->type]->getClientMediaType();
        $this->width     = $sizes[0];
        $this->height    = $sizes[1];
        $this->ratio     = round($this->width/$this->height, 2);
        $this->index     = time();
      }
    }

  }


  public function resize(): string {

    $target = self::DIMENSIONS[$this->type];
    $targetFile = $this->type."_".$this->index.".".$this->extension;

    if($target['ratio'] > $this->ratio) { //Uploaded: portrait, target: landscape
      $sourceWidth  = $this->width;
      $sourceHeight = round($this->width/$target['ratio']);
      $sourceX = 0;
      $sourceY = round(($this->height-$sourceHeight)/2);
    } elseif($target['ratio'] < $this->ratio) { //Uploaded: landscape, target: portrait
      $sourceWidth  = round($this->height*$target['ratio']);
      $sourceHeight = $this->height;
      $sourceX = round(($this->width-$sourceWidth)/2);
      $sourceY = 0;
    } else { // Equals
      $sourceWidth  = $this->width;
      $sourceHeight = $this->height;
      $sourceX = 0;
      $sourceY = 0;
    }

    switch($this->mimetype) {
      case "image/jpeg": $source = imagecreatefromjpeg($this->tmp_name);
      break;
      case "image/png":  $source = imagecreatefrompng($this->tmp_name);
      break;
    }

    $canvas = imagecreatetruecolor($target['width'], $target['height']);
    $resampled = imagecopyresampled($canvas, $source, 0, 0, $sourceX, $sourceY, $target['width'], $target['height'], $sourceWidth, $sourceHeight);

    if($resampled) {

      switch($this->mimetype) {
        case "image/jpeg": $success = @imagejpeg($canvas, $this->tmp.$targetFile, 80);
        break;
        case "image/png":  $success = @imagepng($canvas, $this->tmp.$targetFile, 8);
        break;
      }

      if($success) {
        imagedestroy($source);
        imagedestroy($canvas);
        return $this->tmp.$targetFile;
      } else { throw new \Error(self::ERRORS[7]); }

    }

  }

}
