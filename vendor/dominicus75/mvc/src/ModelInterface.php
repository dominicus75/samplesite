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
    \PDO $connection,
    string $contentId,
    string $tableName
  );

  public function getTableName(): string;

  public function getContentId(): string;

  public function set(string $id, array $content): void;

  public function get(string $id);

  public function insert(string $id, array $content array $content): bool;

  public function select(string $id, array $params): array;

  public function update(string $id, array $params): bool;

  public function delete(string $id): bool;

}
