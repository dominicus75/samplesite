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
    string $tableName,
    ?string $contentId = null,
    ?array $content = null
  );

  public function getTableName(): string;

  public function getContentId();

  public function setContent(?array $content = null): void;

  public function getContent(): ?array;

  public function insert(string $id, array $content): bool;

  public function select(string $id, array $params): array;

  public function update(string $id, array $params): bool;

  public function delete(string $id): bool;

}
