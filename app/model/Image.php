<?php
/*
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Application\Model;

use \Dominicus75\Http\UploadedFile;

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

  /**
   *
   * @var UploadedFile
   *
   */
  private UploadedFile $file;

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
   * @param array $files
   *
   */
  public function __construct(array $files) {

    $this->type = key($files);
    $this->file = $files[$this->type];

    if(!in_array($this->file->getClientMediaType(), self::ALLOWED_MIMES)) {
      throw new \Error($this->file->getClientMediaType().' is not allowed');
    } elseif($this->file->getError() > 0) {
      throw new \Error($this->file::ERROR_MESSAGES[$this->file->getError()]);
    } elseif(!is_uploaded_file($this->file->getTmpName())) {
      throw new \Error($this->file::ERROR_MESSAGES[UPLOAD_ERR_NO_FILE]);
    } else {
      $sizes = getimagesize($this->file->getTmpName());
      if(!$sizes) {
        throw new \Error($this->file->getTmpName().' is not an image file');
      } else {
        $this->tmp       = dirname(dirname(__DIR__)).'/public/upload/tmp/';
        $this->width     = $sizes[0];
        $this->height    = $sizes[1];
        $this->ratio     = round($this->width/$this->height, 2);
        $this->index     = time();
      }
    }

  }


  public function resize(): string {

    $target = self::DIMENSIONS[$this->type];
    $targetFile = $this->type."_".$this->index.".".$this->file->getExtension();

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

    switch($this->file->getClientMediaType()) {
      case "image/jpeg": $source = imagecreatefromjpeg($this->file->getTmpName());
      break;
      case "image/png":  $source = imagecreatefrompng($this->file->getTmpName());
      break;
    }

    $canvas = imagecreatetruecolor($target['width'], $target['height']);
    $resampled = imagecopyresampled($canvas, $source, 0, 0, $sourceX, $sourceY, $target['width'], $target['height'], $sourceWidth, $sourceHeight);

    if($resampled) {

      switch($this->file->getClientMediaType()) {
        case "image/jpeg": $success = @imagejpeg($canvas, $this->tmp.$targetFile, 80);
        break;
        case "image/png":  $success = @imagepng($canvas, $this->tmp.$targetFile, 8);
        break;
      }

      if($success) {
        imagedestroy($source);
        imagedestroy($canvas);
        return $targetFile;
      } else { throw new \Error($this->file::ERROR_MESSAGES[\UPLOAD_ERR_CANT_WRITE]); }

    } else { throw new \Error('Image resizing was not a success'); }

  }

}
