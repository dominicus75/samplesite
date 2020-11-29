<?php
/*
 * @file ModelInterface.php
 * @package MVC
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\MVC;

interface ModelInterface {

  public function __construct(
    \ArrayAccess $pdoConfig,
    $contentID = ''
  );

  public function getTableName(): string;

  public function hasPrimaryKey(): bool;

  public function getPrimaryKey();

  public function setContent(array $content = []): void;

  public function updateContent(array $content = []): void;

  public function getContent(): array;

  public function setField($field, $value): void;

  public function updateField($field, $value): void;

  public function insert(): bool;

  public function select(array $params = []): ?array;

  public function update(): bool;

  public function delete(): bool;

}
