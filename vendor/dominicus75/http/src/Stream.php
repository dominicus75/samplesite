<?php
/*
 * @file Stream.php
 * @package samplesite
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\Http;

class Stream implements \Psr\HttpMessage\StreamInterface
{

  private const READABLE = '/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/';
  private const WRITABLE = '/a|w|r\+|rb\+|rw|x|c/';

  protected resource $stream;
  protected array $meta;


  public function __construct($input) {

    if(is_string($input)) {
      $resource = fopen('php://temp', 'rw+');
      fwrite($resource, $input);
      $stream = $resource;
    } else if(is_resource($input)) {
      $stream = $input;
    } else {
      throw new InvalidArgumentException(
        'First argument to Stream::__construct() must be a string or resource.'
      );
    }

    $this->stream = $stream;
    $this->meta = stream_get_meta_data($this->stream);

  }


  public function __toString(): string {

    if (!isset($this->stream)) { return ''; }
    if ($this->isSeekable()) { $this->rewind(); }
    return $this->getContents();

  }


  public function close(): void {

    if($this->stream) { fclose($this->stream); }
    $this->detach();

  }


  public function detach(): ?resource {

    if (!isset($this->stream)) { return null; }
    $result = $this->stream;
    unset($this->stream);
    return $result;

  }


  public function getSize(): ?int {

    if (!isset($this->stream)) { return null; }

    $result = fstat($this->stream);
    if (is_array($result) && array_key_exists('size', $result)) {
      return $result['size'];
    }

    return null;

  }


  public function tell(): int {
    if (!isset($this->stream)) { throw new \RuntimeException('Stream is detached'); }
    return ftell($this->stream);
  }


  public function eof(): bool {
    if (!isset($this->stream)) { return false; }
    return feof($this->stream);
  }


  public function isSeekable(): bool {
    if (!isset($this->stream)) { return false; }
    return $this->getMetadata('seekable')
  }


  public function seek(int $offset, int $whence = SEEK_SET): void {

    if (!isset($this->stream)) { throw new \RuntimeException('Stream is detached'); }

    if (!$this->isSeekable()) {
      throw new RuntimeException('Stream is not seekable');
    }

    if (-1 === fseek($this->stream, $offset, $whence)) {
      throw new RuntimeException('Unable to seek to stream position '.$offset.' with whence '.$whence);
    }

  }


  public function rewind(): void {

    if (!isset($this->stream)) { throw new RuntimeException('Stream is detached'); }
    $this->seek(0);

  }


  public function isWritable(): bool {
    if (!isset($this->stream)) { return false; }
    return (bool)preg_match(self::WRITABLE, $this->getMetadata('mode'));
  }


  public function write(string $string): int {

    if (!isset($this->stream)) { throw new RuntimeException('Stream is detached'); }

    if (!$this->isWritable()) {
      throw new RuntimeException('Cannot write to a non-writable stream');
    }

    if (false === $result = fwrite($this->stream, $string)) {
      throw new RuntimeException('Unable to write to stream');
    }

    return $result;

  }


  public function isReadable(): bool {
    if (!isset($this->stream)) { return false; }
    return (bool)preg_match(self::READABLE, $this->getMetadata('mode'));
  }


  public function read(int $length): string {

    if (!isset($this->stream)) { throw new RuntimeException('Stream is detached'); }
    if (!$this->readable) { throw new RuntimeException('Cannot read from non-readable stream'); }
    if ($length < 0) { throw new RuntimeException('Length parameter cannot be negative'); }
    if (0 === $length) { return ''; }

    if (false === $result = fread($this->stream, $length)) {
      throw new RuntimeException('Unable to read from stream');
    }

    return $result;

  }


  public function getContents(): string {

    if (!isset($this->stream)) {
      throw new RuntimeException('Stream is detached');
    }

    if (false === $result = stream_get_contents($this->stream)) {
      throw new RuntimeException('Unable to read stream contents');
    }

    return $result;

  }


  public function getMetadata(string $key = null) {

   if(!isset($this->stream)) { return null; }

   if(is_null($key)) {
      return $this->meta;
   } else {
      return array_key_exists($key, $this->meta) ? $this->meta[$key] : null;
   }

  }

}
